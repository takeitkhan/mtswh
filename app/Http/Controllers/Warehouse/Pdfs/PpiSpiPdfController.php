<?php

namespace App\Http\Controllers\Warehouse\Pdfs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   // <-- Required for DB::transaction
use Mpdf\Mpdf;
use App\Services\PdfService;
use App\Models\PpiSpi;
use App\Helpers\Warehouse\PpiSpiHelper;
use App\Models\PpiSpiStatus;
use App\Models\PpiSpiDispute;
use App\Models\GlobalSettings;
use App\Models\Warehouse;
use App\Models\PpiSpiSource;



class PpiSpiPdfController extends Controller
{
    
    protected $model;
    protected $ppiSpiStatusController;

    public function __construct()
    {
        $this->model = new PpiSpi();
    }
    
    
    /**
     * Mark challan as printed in the database
     * (We keep warehouse_code and status for clarity / future use)
     */
    private function markChallanPrinted(string $warehouse_code, int $ppi_id, string $status)
    {
        $warehouse_id = Warehouse::where('code', $warehouse_code)
                                            ->firstOrFail()
                                            ->id;
        
        return PpiSpiStatus::updateOrCreate(
            [
                'ppi_spi_id'   => $ppi_id,
                'warehouse_id' => $warehouse_id,
                'status_for'   => 'Ppi',
                'message'      => 'PPI Delivery Challan has been downloaded!',
                'status_order' => 12,
                'status_format'=> 'Optional',
                'status_type' => 'success',
                'ppi_spi_product_id' => null,
                'code'         => 'ppi_challan_pdf_printed'
            ],
            [
                'status_value'         => 1,
                'action_performed_by'  => auth()->id(),
            ]
        );
    }

    /**
     * Generate PDF content for a given PPI
     * Keep warehouse_code and status in signature for parity with routes,
     * but only ppi_id is required to load data.
     */
    private function makeChallanPdf(string $warehouse_code, int $ppi_id, string $status = '')
    {
        // Ensure PPI exists
        $ppi = PpiSpi::with(['ppi_products.productInfo.unit'])->findOrFail($ppi_id);
        $ppiActionPerformedBy = PpiSpiStatus::where('ppi_spi_id', $ppi_id)
                                ->where('status_for', 'Ppi')
                                ->where('code', 'ppi_new_product_added_to_stock')
                                ->orderBy('id', 'desc')->first();
        $products = $ppi->ppi_products;

        // Safe access to performedBy
        $performedBy = $ppiActionPerformedBy ? $ppiActionPerformedBy->performedBy : null;
        
        $sources = PpiSpiSource::where('ppi_spi_id', $ppi_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Ensure tempDir exists & writable
        $tempDir = storage_path('app/mpdf_temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mpdf = new Mpdf([
            'format'       => 'A4',
            'margin_top'   => 25,
            'margin_bottom'=> 25,
            'margin_header'=> 10,
            'margin_footer'=> 10,
            'tempDir'      => $tempDir,
        ]);

        $logoPath          = public_path('assets/images/logo.jpg');
        $locationLogoPath  = public_path('assets/images/pin.png');
        $footerLogoPath    = public_path('assets/images/footer_logo.jpg');
        $telephoneLogoPath = public_path('assets/images/smartphone.png');
        $globeLogoPath     = public_path('assets/images/globe.png');
        $maxRows           = 22;

        // === HEADER ===
        $headerHtml = view('pdf.challan-header', compact('ppi', 'logoPath'))->render();
        $mpdf->SetHTMLHeader($headerHtml);

        // === FOOTER ===
        $footerHtml = view('pdf.challan-footer', compact('footerLogoPath', 'locationLogoPath', 'telephoneLogoPath', 'globeLogoPath'))->render();
        $mpdf->SetHTMLFooter($footerHtml);

        $html = view('pdf.challan', compact(
            'ppi', 'products', 'logoPath', 'footerLogoPath', 'locationLogoPath',
            'telephoneLogoPath', 'globeLogoPath', 'maxRows', 'performedBy', 'sources'
        ))->render();

        $mpdf->WriteHTML($html);

        // Return PDF bytes/string
        return $mpdf->Output('', 'S');
    }

    /**
     * View PDF in Browser (INLINE)
     * Route: GET /ppi/{warehouse_code}/{ppi_id}/{status?}/challan/view
     */
    public function viewChallanPdf(Request $request, string $warehouse_code, int $ppi_id, string $status = '')
    {
        try {
            $pdfContent = $this->makeChallanPdf($warehouse_code, $ppi_id, $status);
            
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="ppi_challan_' . $ppi_id . '.pdf"'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Public GET route: just view/download the challan PDF
     * (Does not modify DB)
     * Route: GET /ppi/{warehouse_code}/{ppi_id}/{status?}/challan/pdf
     */
    public function generateChallanPdf(Request $request, string $warehouse_code, int $ppi_id, string $status = '')
    {
        try {
            $pdfContent = $this->makeChallanPdf($warehouse_code, $ppi_id, $status);
            $filename = "ppi_challan_{$ppi_id}_{$warehouse_code}.pdf";
    
            return response()->streamDownload(function() use ($pdfContent) {
                echo $pdfContent;
            }, $filename, ['Content-Type' => 'application/pdf']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Public POST route: mark status and generate PDF (server-side)
     * Route: POST /ppi/{warehouse_code}/{ppi_id}/{status}/challan-pdf
     *
     * This expects a POST (use a form submission to call it if you want to open in new tab)
     */
    public function ppiChallanPdfPrintedAction(Request $request, string $warehouse_code, int $ppi_id, string $status)
    {
        try {
            DB::transaction(function () use ($warehouse_code, $ppi_id, $status) {
                $this->markChallanPrinted($warehouse_code, $ppi_id, $status);
            });
    
            return response()->json(['success' => true]);
        } 
        catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function generate()
    {
        try {
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'margin_top' => 20,
                'margin_bottom' => 20,
            ]);

            // Logo path (top-right and bottom-left)            
            $logoPath = public_path('assets/images/logo.jpg'); // change to your logo
            $footerLogoPath = public_path('assets/images/footer_logo.jpg'); // change to your logo
            $locationLogoPath = public_path('assets/images/pin.png'); // change to your logo
            $telephoneLogoPath = public_path('assets/images/smartphone.png'); // change to your logo
            $globeLogoPath = public_path('assets/images/globe.png'); // change to your logo
            // Set watermark image
            $watermarkPath = public_path('assets/images/watermark_logo.jpg');
            $mpdf->SetWatermarkImage($watermarkPath, 0.1);
            $mpdf->showWatermarkImage = true;


            // Maximum 11 rows
            $maxRows = 11;

            // Prepare HTML
            $html = '
        <html>
        <head>
        <style>
            body { font-family: sans-serif; font-size: 12px; }
            .header-table td { padding: 3px; vertical-align: top; }
            .material-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            .material-table th, .material-table td { border: 1px solid #000; padding: 5px; text-align: center; }
            .material-table th { background-color: #f0f0f0; }
            .note-box { width: 100%; border: 1px solid #000; padding: 5px; margin-top: 10px; }
            .footer-box { width: 45%; border: 1px solid #000; padding: 5px; display: inline-block; vertical-align: top; margin-top: 20px; }
            .logo-top { position: absolute; top: 10px; right: 10px; width: 100px; }
            .logo-bottom { position: absolute; bottom: 0px; left: 10px; width: 150px; margin-bottom: 15px; }
            .watermark {
                position: fixed;
                top: 40%;
                left: 30%;
                opacity: 0.05;
                transform: translate(-50%, -50%);
                z-index: -1000;
                font-size: 100px;
                color: #F1F1F1;
            }
            .footer_icons { width: 10px; height: 10px; }
        </style>
        </head>
        <body>
            

            <h2 style="text-align:center; text-decoration: underline;">DELIVERY CHALLAN</h2>

            <table class="header-table" width="100%">
                <tr>
                    <td width="18%">
                        <img src="' . $logoPath . '" class="logo-top" />
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td width="18%">SPI No:</td>
                                <td width="32%">23094823094</td>
                                <td width="22%">Requested By (PM):</td>
                                <td width="28%">Mohammad Mamunur Rashid </td>
                            </tr>
                            <tr>
                                <td>Date:</td>
                                <td>24/11/2025</td>
                                <td>Approved By:</td>
                                <td>Md. Khalakuzzaman Khan</td>
                            </tr>
                            <tr>
                                <td>Source:</td>
                                <td>waresadfasdfas</td>
                                <td>Warehouse In-Charge:</td>
                                <td>Md. Jakir Hossain</td>
                            </tr>
                            <tr>
                                <td>Project Name:</td>
                                <td>Bangladesh Bangla Link Telecommunication</td>
                                <td>Received By:</td>
                                <td>Mr. Mokbul Hossain</td>
                            </tr>
                            <tr>
                                <td>Site Name:</td>
                                <td>2345234321asfas</td>
                                <td>Expected Return Date (if temporary):</td>
                                <td>adfasdfasd</td>
                            </tr>
                        </table>                        
                    </td>
                </tr>                
            </table>

            <div class="note-box"><strong>Note</strong></div>

            <h4 style="margin-top:20px;">MATERIAL DETAILS</h4>

            <table class="material-table">
                <tr>
                    <th>SL</th>
                    <th>Material Description</th>
                    <th>Brand</th>
                    <th>Condition<br>(New/Used/Faulty)</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th>Remarks</th>
                </tr>';

            // Generate empty rows up to $maxRows
            for ($i = 1; $i <= $maxRows; $i++) {
                $html .= '
                <tr>
                    <td>' . $i . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>';
            }

            $html .= '
            </table>

            <table style="width:100%; margin-top:30px; border-collapse: collapse;">
                <tr>
                    <td style="width:50%; vertical-align: top; border-top: 1px solid #000; padding-top:10px;">
                        <strong>Delivered By</strong><br/>
                        Name<br/>
                        Contact<br/>
                        Company Name<br/>
                        ID<br/>
                        Designation
                    </td>
                    <td style="width:50%; vertical-align: top; border-top: 1px solid #000; padding-top:10px;">
                        <strong>Received By</strong><br/>
                        Name<br/>
                        Contact<br/>
                        Company Name<br/>
                        ID<br/>
                        Designation
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>


            <img src="' . $footerLogoPath . '" class="logo-bottom" /><br/>
            <small><img src="'. $locationLogoPath .'" class="footer_icons"/> Plot # Cha-2, Flat: 2nd Floor, Uttar Badda, Gulshan, Dhaka - 1212<br/>
            <img src="'. $telephoneLogoPath .'" class="footer_icons"/> +88 01844217317, +88 01844217300 <img src="'. $globeLogoPath .'" class="footer_icons"/> info@mtsbd.net, www.mtsbd.net</small>

        </body>
        </html>
        ';

            $mpdf->WriteHTML($html);

            return response($mpdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // ============================================
    // SPI DELIVERY CHALLAN PDF METHODS
    // ============================================

    /**
     * Mark delivery challan as generated in database
     */
    private function markDeliveryChallanGenerated(string $warehouse_code, int $spi_id, string $status)
    {
        $warehouse_id = Warehouse::where('code', $warehouse_code)
                                            ->firstOrFail()
                                            ->id;
        
        return PpiSpiStatus::updateOrCreate(
            [
                'ppi_spi_id'   => $spi_id,
                'warehouse_id' => $warehouse_id,
                'status_for'   => 'Spi',
                'code'         => 'spi_delivery_challan_generated'
            ],
            [
                'message'      => 'SPI Delivery Challan has been generated',
                'status_order' => 14,
                'status_format'=> 'Main',
                'status_type' => 'success',
                'action_performed_by' => auth()->id(),
            ]
        );
    }

    /**
     * View Delivery Challan PDF in Browser (INLINE)
     * Route: GET /spi/{warehouse_code}/{spi_id}/{status?}/challan/view
     */
    public function viewDeliveryChallanPdf(Request $request, string $warehouse_code, int $spi_id, string $status = '')
    {
        try {
            $pdfContent = $this->makeDeliveryChallanPdf($warehouse_code, $spi_id, $status);
            
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="spi_delivery_challan_' . $spi_id . '.pdf"'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Download Delivery Challan PDF
     * Route: GET /spi/{warehouse_code}/{spi_id}/{status?}/challan/pdf
     */
    public function generateDeliveryChallanPdf(Request $request, string $warehouse_code, int $spi_id, string $status = '')
    {
        try {
            $pdfContent = $this->makeDeliveryChallanPdf($warehouse_code, $spi_id, $status);
            $filename = "spi_delivery_challan_{$spi_id}_{$warehouse_code}.pdf";
    
            return response()->streamDownload(function() use ($pdfContent) {
                echo $pdfContent;
            }, $filename, ['Content-Type' => 'application/pdf']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark Delivery Challan as Generated
     * Route: POST /spi/{warehouse_code}/{spi_id}/{status}/challan-pdf
     */
    public function spiChallanPdfGeneratedAction(Request $request, string $warehouse_code, int $spi_id, string $status)
    {
        try {
            DB::transaction(function () use ($warehouse_code, $spi_id, $status) {
                $this->markDeliveryChallanGenerated($warehouse_code, $spi_id, $status);
            });
    
            return response()->json(['success' => true]);
        } 
        catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Make SPI Delivery Challan PDF
     * (Similar to PPI challan but for SPI)
     */
    private function makeDeliveryChallanPdf(string $warehouse_code, int $spi_id, string $status = '')
    {
        try {
            // Fetch SPI with related data
            $spi = PpiSpi::where('id', $spi_id)
                            ->where('action_format', 'Spi')
                            ->with(['spiProducts.productInfo.unit'])
                            ->firstOrFail();

            $warehouse = Warehouse::where('code', $warehouse_code)->firstOrFail();

            // Get warehouse manager info
            $spiActionPerformedBy = PpiSpiStatus::where('ppi_spi_id', $spi_id)
                                ->where('status_for', 'Spi')
                                ->where('code', 'spi_all_steps_complete')
                                ->orderBy('id', 'desc')->first();
            
            $performedBy = $spiActionPerformedBy ? $spiActionPerformedBy->performedBy : null;

            // Ensure tempDir exists & writable
            $tempDir = storage_path('app/mpdf_temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $mpdf = new Mpdf([
                'format'       => 'A4',
                'margin_top'   => 25,
                'margin_bottom'=> 25,
                'margin_header'=> 10,
                'margin_footer'=> 10,
                'tempDir'      => $tempDir,
            ]);

            $logoPath          = public_path('assets/images/logo.jpg');
            $locationLogoPath  = public_path('assets/images/pin.png');
            $footerLogoPath    = public_path('assets/images/footer_logo.jpg');
            $telephoneLogoPath = public_path('assets/images/smartphone.png');
            $globeLogoPath     = public_path('assets/images/globe.png');

            // === HEADER ===
            $headerHtml = view('pdf.spi.delivery-challan-header', compact('spi', 'warehouse', 'logoPath'))->render();
            $mpdf->SetHTMLHeader($headerHtml);

            // === FOOTER ===
            $footerHtml = view('pdf.spi.delivery-challan-footer', compact('footerLogoPath', 'locationLogoPath', 'telephoneLogoPath', 'globeLogoPath'))->render();
            $mpdf->SetHTMLFooter($footerHtml);

            // === BODY ===
            $html = view('pdf.spi.delivery-challan-body', compact(
                'spi', 'warehouse', 'logoPath', 'footerLogoPath', 'locationLogoPath',
                'telephoneLogoPath', 'globeLogoPath', 'performedBy'
            ))->render();

            $mpdf->WriteHTML($html);
            
            return $mpdf->Output('', 'S');

        } catch (\Exception $e) {
            throw $e;
        }
    }
}

