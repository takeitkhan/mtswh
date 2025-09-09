<?php
/**
 * 1st parameter Set Must Warehouse code
 * use = request()->get('warehouse_code') during route name call
 */
/** PPI */
Route::group(['key' => 'SPI', 'prefix' => '/spi', 'as' => 'spi_'], function () {

    Route::get('/api/get', ['uses' => 'SpiController@apiGet'])->name('api_get');
    Route::get('/manage/', ['uses' => 'SpiController@index', 'title' => 'Manage SPI', 'show' => 'Yes', 'position' => 'Left'])->name('index');
    Route::get('/create', ['uses' => 'SpiController@create', 'title' => 'Create SPI', 'show' => 'Yes', 'position' => 'Left'])->name('create');
    Route::post('/store', ['uses' => 'SpiController@store'])->name('store');
    Route::get('/edit/{id}', ['uses' => 'SpiController@edit', 'title' => 'Edit'])->name('edit');
    Route::post('/update', ['uses' => 'SpiController@update'])->name('update');
    Route::delete('/delete/{id}', ['uses' => 'SpiController@destroy', 'title' => 'Delete SPI'])->name('destroy');
    Route::get('/selected-product-details-info', ['uses' => 'SpiController@selectedProductDetailsInfo'])->name('selected_product_details_info');
    Route::get('/history/{id}', ['uses' => 'PpiSpiHistoryController@history'])->name('history');
    Route::get('/spi-id-lookup', ['uses' => 'SpiController@lookup'])->name('spi_lookup');


    /** SPI Product
     * 1st parameter Set Must Warehouse code
     * use = request()->get('warehouse_code') during route name call
     */
    Route::get('/product/add', ['uses' => 'SpiProductController@add', 'title' => 'Add Product to Spi'])->name('product_add');
    Route::post('/product/store', ['uses' => 'SpiProductController@store'])->name('product_store');
    Route::get('/product/edit/{id}', ['uses' => 'SpiProductController@edit', 'title' => 'Edit Ppi Product'])->name('product_edit');
    Route::post('/product/update', ['uses' => 'SpiProductController@update'])->name('product_update');
    Route::delete('/product/delete/{id}', ['uses' => 'SpiProductController@destroy', 'title' => 'Delete Product from Spi'])->name('product_destroy');
    Route::post('/import-product-from-spi/', ['uses' => 'SpiProductController@importProductFromAnotherSpi', 'title' => 'Import Product from Another SPI'])->name('product_import_from_another_spi');


    /** Barcode Generate
     * 1st parameter Set Must Warehouse code
     * use = request()->get('warehouse_code') during route name call
     */
    Route::get('get-line-item/{id}', ['uses' => 'BarcodeController@getSpiLineItem', 'title' => 'Physical Validate Page'])->name('get_line_item');
    //Route::get('/generate-barcode', ['uses' => 'PpiBarcodeController@indexOf'])->name('barcode_generator');
    //Product Stock IN
    Route::post('/product-stock-out', ['uses' => 'ProductStockController@stockOut'])->name('product_stock_out');
    //Existing product Stock
    //Route::any('/existing-product-check-during-stock', ['uses' => 'ProductStockController@existingProductCheckByBarcode'])->name('existing_product_check_during_stock');


    //Transfer
    Route::post('generate-transfer', ['uses' => 'TransferController@generateSpiTransfer'])->name('generate_transfer');
    Route::post('transfer', ['uses' => 'TransferController@spiTransfer', 'title' => 'Spi Transfer'])->name('transfer');
    Route::post('buy-product-from-vendor', ['uses' => 'TransferController@buyProductFromVendor', 'title' => 'Product Purchase From Vendor'])->name('buy_product_form_vendor');

});

/**
 * 1st parameter Set Must Warehouse code
 * use request()->get('warehouse_code')
 */
/** PPI Action */
Route::group(['key' => 'SPI Action', 'prefix' => '/spi', 'as' => ''], function () {
    $ppiAction = \App\Helpers\Warehouse\PpiSpiHelper::spiStatusHandler();
    foreach ($ppiAction as $key => $data) {
        if ($data['is_route'] == true) {
            if ($data['route_upload'] == true) {
                Route::any('/action/{ppi_id}/{action}/' . $key, ['uses' => 'PpiSpiStatusController@getSpiActionStatus', 'title' => $data['route_title']])->name($key . '_action');
            } else {
                Route::any('/action/{ppi_id}/{action}/' . $key, ['uses' => 'PpiSpiStatusController@getSpiActionStatus'])->name($key . '_action');
            }
        }
    }
}); // End PPI Action

Route::group(['key' => 'SPI'], function () {
    Route::get('/manage_lended/', ['uses' => 'SpiController@lendedProductsForSpi', 'title' => 'SPI Lended', 'show' => 'Yes', 'position' => 'Left'])->name('spi_lended');
});
?>
