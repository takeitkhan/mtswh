<?php
 Route::group(['key' => 'Warehouse','prefix' => 'warehouse', 'as' => 'warehouse_', 'middleware' => 'warehouse'], function(){
    Route::get('/manage', ['uses'=>'WarehouseController@index', 'title' => 'Manage Warehouse', 'show' => 'Yes', 'position' => 'Top,Left'])->name('index');
    Route::get('/create', ['uses'=>'WarehouseController@create', 'title' => 'Add', 'show' => 'Yes', 'position' => 'Left'])->name('create');
    Route::post('/store', 'WarehouseController@store')->name('store');
    Route::get('/edit/{id}', ['uses' => 'WarehouseController@edit', 'title' => 'Edit'])->name('edit');
    Route::post('/update', 'WarehouseController@update')->name('update');
    Route::delete('/delete/{id}', ['uses'=>'WarehouseController@destroy', 'title' => 'Delete'])->name('destroy');
    Route::get('/notification/{status_id}', ['uses'=>'WarehouseController@notofication'])->name('notification');
    Route::post('/notification/clear-all', ['uses'=>'WarehouseController@notoficationClearAll'])->name('notification_clear_all');
    Route::get('{wh_code}', ['uses' => 'Warehouse\SingleWarehouseController@index', 'title' => 'View Warehouse'])->name('single_index');
});

Route::group(['key' => 'Report','prefix' => 'report', 'as' => 'report_', 'middleware' => ['auth', 'user']], function() {
    Route::get('/product-stock', ['uses' => 'WarehouseController@productStock', 'title' => 'Product Stock', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('product_stock');
    Route::get('/api/get-product-stock', ['uses' => 'WarehouseController@apiGetProductStock'])->name('api_get_product_stock');
    Route::get('/product-stock/{product_id}', ['uses' => 'WarehouseController@productStock',  'title' => 'Product Stock Details'])->name('product_stock_details');

    //Siite based Product PPI
    Route::get('/ppi-site-based-product-report', ['uses' => 'WarehouseController@ppiSiteBasedProductReport', 'title' => 'Product Report Of Site (Ppi)', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('ppi_site_based_product_report');
    Route::get('/api/ppi-site-based-product-report', ['uses' => 'WarehouseController@apiGetPpiSiteBasedProductReport'])->name('api_get_ppi_site_based_product_report');
    Route::get('/spi-site-based-product-report', ['uses' => 'WarehouseController@spiSiteBasedProductReport', 'title' => 'Product Report Of Site (Spi)', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('spi_site_based_product_report');
    Route::get('/ppi-product-to-spi/{ppi_product_id}', ['uses' => 'WarehouseController@ppiProductToSpi', 'title' => 'Ppi Product Use To Spi Product'])->name('ppi_product_to_spi');
    
    
    // Accumulated Report
    Route::get('/ppi-spi-accumulated', ['uses' => 'WarehouseController@ppiSpiAccumulated', 'title' => 'Ppi Spi Accumulated Report', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('ppi_spi_accumulated_report');

    //Vendor
    Route::get('/vendor-report', ['uses' => 'WarehouseController@vendorReport', 'title' => 'Vendor Report', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('vendor_report');
    Route::get('/purchase-vendor-report', ['uses' => 'WarehouseController@purchaseVendorReport', 'title' => 'Purchase Vendor', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('purchase_vendor_report');
    //scrapped
    Route::get('/scrapped-product', ['uses' => 'WarehouseController@scrappedProduct', 'title' => 'Scrapped Product', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('scrapped_product');
    Route::get('/scrapped-product-details/{product_id}', ['uses' => 'WarehouseController@scrappedProductDetails', 'title' => 'Scrapped Product Details'])->name('scrapped_product_details');
    Route::get('/api/get-scrapped-product', ['uses' => 'WarehouseController@apiGetScrappedProduct'])->name('api_get_scrapped_product');

    //Faulty
    Route::get('/faulty-product', ['uses' => 'WarehouseController@faultyProduct', 'title' => 'Faulty Product', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('faulty_product');
    Route::get('/faulty-product-details/{product_id}', ['uses' => 'WarehouseController@faultyProductDetails', 'title' => 'Faulty Product Details'])->name('faulty_product_details');
    Route::get('/api/get-faulty-product', ['uses' => 'WarehouseController@apiGetFaultyProduct'])->name('api_get_faulty_product');

    //Project lended Report
    Route::any('lended-from-project', ['uses' => 'WarehouseController@lendedFromReport', 'title' => 'Product Lend Report of Project'])->name('lended_from_project');
    Route::any('lended-from-project-start-return', ['uses' => 'WarehouseController@startlendProjectReturn', 'title' => 'Product Lend Return Permit of Project'])->name('lended_from_project_start_return');
});

/** Single Warehouse */
Route::group(['prefix'=> '{wh_code?}','namespace' => 'Warehouse', 'show_for' => 'Warehouse', 'middleware' => 'warehouse'], function(){
    /**
     * 1st parameter Set Must Warehouse code
     * use = request()->get('warehouse_code') during route name call
     */
    //product
    Route::group(['key' => 'Product','prefix' => '/product', 'as' => 'product_'], function(){

        Route::get('/api/get', ['uses' => 'ProductController@apiGet'])->name('api_get');
        Route::get('/manage/', ['uses' => 'ProductController@index', 'title' => 'Manage Products', 'show' => 'Yes', 'position' => 'Left'])->name('index');
        Route::get('/create', ['uses' => 'ProductController@create', 'title' => 'Add Products', 'show' => 'Yes', 'position' => 'Left'])->name('create');
        Route::post('/store', ['uses' => 'ProductController@store'])->name('store');
        Route::post('/excel-store', ['uses' => 'ProductController@uploadViaExcel'])->name('excel_store');
        Route::get('/edit/{id}', ['uses' => 'ProductController@edit', 'title' => 'Edit'])->name('edit');
        Route::post('/update', ['uses' => 'ProductController@update'])->name('update');
        Route::delete('/delete/{id}', ['uses'=>'ProductController@destroy', 'title' => 'Delete'])->name('destroy');


        /** Product Category */
        Route::get('-category/manage/', ['uses' => 'ProductCategoryController@index', 'title' => 'Manage Categories','show' => 'Yes', 'position' => 'Left'])->name('category_index');
        Route::post('-category/store', ['uses' => 'ProductCategoryController@store'])->name('category_store');
        Route::get('-category/edit/{id}', ['uses' => 'ProductCategoryController@edit', 'title' => 'Edit Category'])->name('category_edit');
        Route::post('-category/update', ['uses' => 'ProductCategoryController@update'])->name('category_update');
        Route::delete('-category/delete/{id}', ['uses' => 'ProductCategoryController@destroy', 'title' => 'Delete Categories'])->name('category_destroy');

    });//End Product

     /**
     * 1st parameter Set Must Warehouse code
     * use = request()->get('warehouse_code') during route name call
     */
    /** PPI */
    Route::group(['key' => 'PPI','prefix' => '/ppi', 'as' => 'ppi_'], function(){
        Route::get('/api/get', ['uses' => 'PpiController@apiGet'])->name('api_get');
        Route::get('/manage/', ['uses' => 'PpiController@index', 'title' => 'Manage PPI', 'show'=> 'Yes', 'position' => 'Left'])->name('index');
        Route::get('/create', ['uses' => 'PpiController@create', 'title' => 'Create PPI', 'show'=> 'Yes', 'position' => 'Left'])->name('create');
        Route::post('/store', ['uses' => 'PpiController@store'])->name('store');
        Route::get('/edit/{id}', ['uses' => 'PpiController@edit', 'title' => 'Edit'])->name('edit');
        Route::post('/update', ['uses' => 'PpiController@update'])->name('update');
        Route::delete('/delete/{id}', ['uses' => 'PpiController@destroy', 'title' => 'Delete PPI'])->name('destroy');
        Route::get('/history/{id}', ['uses' => 'PpiSpiHistoryController@history'])->name('history');

        /** PPI Product
         * 1st parameter Set Must Warehouse code
        * use = request()->get('warehouse_code') during route name call
        */
        Route::get('/product/add', ['uses' => 'PpiProductController@add', 'title' => 'Add Product to Ppi'])->name('product_add');
        Route::post('/product/store', ['uses' => 'PpiProductController@store'])->name('product_store');
        Route::get('/product/edit/{id}', ['uses' => 'PpiProductController@edit', 'title' => 'Edit Ppi Product'])->name('product_edit');
        Route::post('/product/update', ['uses' => 'PpiProductController@update'])->name('product_update');
        Route::delete('/product/delete/{id}', ['uses' => 'PpiProductController@destroy', 'title' => 'Delete Product from Ppi'])->name('product_destroy');
        Route::post('/import-product-from-ppi/', ['uses' => 'PpiProductController@importProductFromAnotherPpi', 'title' => 'Import Product from Another PPI'])->name('product_import_from_another_ppi');

        /** PPI Set Product
          * 1st parameter Set Must Warehouse code
          * use = request()->get('warehouse_code') during route name call
        */
        Route::get('/set-product/add', ['uses' => 'PpiSetProductController@add', 'title' => 'Create Set Product to Ppi'])->name('set_product_add');
        Route::post('/set-product/store', ['uses' => 'PpiSetProductController@store'])->name('set_product_store');
        Route::delete('/set-product/delete/{id}', ['uses' => 'PpiSetProductController@destroy', 'title' => 'Delete Set from Ppi'])->name('set_product_destroy');
        Route::delete('/delete-product-from-set/{set_id}/{ppi_product_id}', ['uses' => 'PpiSetProductController@destroyProductFromSet', 'title' => 'Delete Product from Set'])->name('product_destroy_from_set');


        /** Barcode Generate
          * 1st parameter Set Must Warehouse code
          * use = request()->get('warehouse_code') during route name call
        */
        Route::get('get-line-item/{id}', ['uses' => 'BarcodeController@getLineItem', 'title' => 'Barcode Page'])->name('get_line_item');
        Route::get('/generate-barcode', ['uses' => 'BarcodeController@indexOf'])->name('barcode_generator');
        //Product Stock IN
        Route::post('/product-stock-in', ['uses' => 'ProductStockController@stockIn'])->name('product_stock_in');
        //Existing product Stock
        Route::any('/existing-product-check-during-stock', ['uses' => 'ProductStockController@existingProductCheckByBarcode'])->name('existing_product_check_during_stock');

    });//End PPi

     /**
     * 1st parameter Set Must Warehouse code
     * use request()->get('warehouse_code')
     */
    /** PPI Action */
    Route::group(['key' => 'PPI Action', 'prefix' => '/ppi', 'as' => ''], function(){
         $ppiAction = \App\Helpers\Warehouse\PpiSpiHelper::ppiStatusHandler();
         foreach($ppiAction as $key => $data){
            if($data['is_route'] == true){
                if($data['route_upload'] == true){
                    Route::any('/action/{ppi_id}/{action}/'.$key, ['uses' => 'PpiSpiStatusController@getPpiActionStatus', 'title' => $data['route_title']])->name($key.'_action');
                }else{
                    Route::any('/action/{ppi_id}/{action}/'.$key, ['uses' => 'PpiSpiStatusController@getPpiActionStatus'])->name($key.'_action');
                }
            }
         }
    }); // End PPI Action

    /**
     * Route For Some Element. e.g. Button, any row or anything
     *  1st parameter Set Must Warehouse code
         * use = request()->get('warehouse_code') during route name call
        */
    Route::group(['key' => 'PPI Elements', 'prefix' => '/ppi', 'as' => ''], function(){
        $ppiElements = \App\Helpers\Warehouse\PpiSpiHelper::ppiElements();
        foreach($ppiElements as $key => $data){
            if($data['is_route'] == true){
                Route::any('/ppi-elements/'.$key, ['uses' => 'PpiSpiStatusController@ppiElements', 'title' => $data['route_title']])->name($key.'_element');
            }
        }
    }); // End PPI Action



    /** SPI */
    include 'spi.php';

});
