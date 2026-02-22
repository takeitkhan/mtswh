<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Warehouse\Pdfs\PpiSpiPdfController;

// Route::group([
//     'key' => 'Warehouse',
//     'prefix' => 'warehouse/{wh_code}/pdfs',
//     'as' => 'warehouse_pdfs_',
//     'middleware' => 'warehouse'
// ], function () {
//     Route::get('/demo', [
//         'uses' => 'App\Http\Controllers\Warehouse\Pdfs\PdfController@generate',
//         'title' => 'PDF Demo',
//         'show' => 'No',
//         'position' => 'Top'
//     ])->name('demo');
// });


//Route::post('/ppi/{warehouse_code}/{ppi_id}/{status}/challan-pdf', [PpiSpiPdfController::class, 'ppiChallanPdfPrintedAction'])->name('ppi_challan_pdf_printed_action');

// Route::post('/ppi/{warehouse_code}/{ppi_id}/{status}/challan/printed', [PpiSpiPdfController::class, 'markChallanPrinted']);
// Route::get('/ppi/{warehouse_code}/{ppi_id}/{status}/challan/pdf', [PpiSpiPdfController::class, 'generateChallanPdf']);

// Route::post('/ppi/{warehouse_code}/{ppi_id}/{status}/challan-pdf', [PpiSpiPdfController::class, 'ppiChallanPdfPrintedAction'])->name('ppi_challan_pdf_printed_now');


// View PDF in Browser (INLINE)
Route::get('/ppi/{warehouse_code}/{ppi_id}/{status?}/challan/view', [PpiSpiPdfController::class, 'viewChallanPdf'])
    ->name('ppi_challan_pdf_preview');

// View PDF with /edit/ in URL (alternative route)
Route::get('/ppi/{warehouse_code}/{ppi_id}/edit/challan/view', [PpiSpiPdfController::class, 'viewChallanPdf'])
    ->name('ppi_challan_pdf_preview_edit');

// Download PDF
Route::get('/ppi/{warehouse_code}/{ppi_id}/{status?}/challan/pdf', [PpiSpiPdfController::class, 'generateChallanPdf'])
    ->name('ppi_challan_pdf_view');

Route::post('/ppi/{warehouse_code}/{ppi_id}/{status}/challan-pdf', [PpiSpiPdfController::class, 'ppiChallanPdfPrintedAction'])
    ->name('ppi_challan_pdf_printed_now');

// ============================================
// SPI DELIVERY CHALLAN PDF ROUTES
// ============================================

// View PDF in Browser (INLINE)
Route::get('/spi/{warehouse_code}/{spi_id}/{status?}/challan/view', [PpiSpiPdfController::class, 'viewDeliveryChallanPdf'])
    ->name('spi_delivery_challan_preview');

// View PDF with /edit/ in URL (alternative route)
Route::get('/spi/{warehouse_code}/{spi_id}/edit/challan/view', [PpiSpiPdfController::class, 'viewDeliveryChallanPdf'])
    ->name('spi_delivery_challan_preview_edit');

// Download PDF
Route::get('/spi/{warehouse_code}/{spi_id}/{status?}/challan/pdf', [PpiSpiPdfController::class, 'generateDeliveryChallanPdf'])
    ->name('spi_delivery_challan_view');

// Mark as Generated
Route::post('/spi/{warehouse_code}/{spi_id}/{status}/challan-pdf', [PpiSpiPdfController::class, 'spiChallanPdfGeneratedAction'])
    ->name('spi_delivery_challan_generated_now');