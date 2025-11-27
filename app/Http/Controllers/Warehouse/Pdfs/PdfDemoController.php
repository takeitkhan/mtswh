<?php

namespace App\Http\Controllers\Warehouse\Pdfs;

use App\Http\Controllers\Controller;
use Mpdf\Mpdf;

class PdfDemoController extends Controller
{
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
}
