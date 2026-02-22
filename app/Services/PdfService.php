<?php

namespace App\Services;

use App\Models\Ppi;
use Mpdf\Mpdf;

class PdfService
{
    /**
     * Generate the challan PDF for a PPI.
     *
     * @param int $ppi_id
     * @param bool $saveToFile Whether to save PDF to storage or output directly
     * @return string|void
     */
    public function generateChallan($ppi_id, $saveToFile = true)
    {
        $ppi = Ppi::findOrFail($ppi_id);


        // Render Blade view to HTML
        $html = view('pdf.challan', compact('ppi'))->render();

        // Create mPDF instance
        $mpdf = new Mpdf([
            'format'  => 'A4',
            'margin_top' => 20,
            'margin_bottom' => 20,
            'tempDir' => storage_path('app/mpdf_temp'),
        ]);

        $mpdf->WriteHTML($html);

        if ($saveToFile) {
            // Save PDF to storage
            $pdfPath = $pdfDir . "/challan_{$ppi_id}.pdf";
            $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);
            return $pdfPath;
        } else {
            // Output PDF directly to browser
            $mpdf->Output("challan_{$ppi_id}.pdf", \Mpdf\Output\Destination::INLINE);
        }
    }
}
