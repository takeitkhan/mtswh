<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Warehouse\Pdfs\PdfDemoController;

Route::group([
    'key' => 'Warehouse',
    'prefix' => 'warehouse/{wh_code}/pdfs',
    'as' => 'warehouse_pdfs_',
    'middleware' => 'warehouse'
], function () {

    Route::get('/demo', [
        'uses' => 'App\Http\Controllers\Warehouse\Pdfs\PdfDemoController@generate',
        'title' => 'PDF Demo',
        'show' => 'Yes',
        'position' => 'Top'
    ])->name('demo');
});
