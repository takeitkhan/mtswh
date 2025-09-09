<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Helpers\Query;

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/upload/routelist', [App\Http\Controllers\HomeController::class, 'uploadRoutes'])->name('upload_routelist');
Route::get('/404', ['uses' => 'App\Http\Controllers\HomeController@error404'])->name('404');
Route::get('/502', ['uses' => 'App\Http\Controllers\HomeController@error502'])->name('502');

Route::group([
    'middleware' => array('auth', 'user'),
    'namespace' => 'App\Http\Controllers',
], function () {
    Route::get('/', function () {
        return redirect()->route('admin_dashboard');
    });
    Route::get('/404', ['uses' => 'HomeController@error404'])->name('404');
    /**
     * Route Property Custom
     * key => Group Name
     * title => Route Custom Title
     * show => is it show in any menu (Value: Yes, No)
     * position =>  (Value: Left, Right, Top, Bottom)
     * show_for => Which Show Menu After Request Url
     * show_as => shwo as Routegroup Show as EX: Permission, User, All (Value: Yes, No)
     * param => Set single or multiple parameter As Array
     * icon => use font awesome icon
     */

    Route::get('testview', 'TestController@index');

    Route::group(['key' => '', 'as' => 'admin_'], function () {
        Route::get('/dashboard', ['uses' => 'HomeController@dashboard', 'title' => 'Dashboard', 'show' => 'Yes', 'icon' => 'fas fa-th', 'position' => 'Top,Left'])->name('dashboard');
      	Route::get('/notifications', ['uses' => 'HomeController@notifications', 'title' => 'Notifications', 'show' => 'Yes', 'icon' => 'fa-regular fa-bell', 'position' => 'Top,Left'])->name('notifications');
        Route::get('/global-settings', ['uses' => 'HomeController@globalSettings', 'title' => 'Global Settings', 'show' => 'Yes', 'icon' => 'fas fa-cog', 'position' => 'Top,Left'])->name('global_settings');
        Route::post('global-settings/update', 'HomeController@globalSettingsUpdate')->name('global_settings_update');
    });

    /** RouteList */
    Route::group(['prefix' => 'superadmin'], function () {
        require_once 'modules/superadmin.php';
    });

    /** Warehouse  */
    require_once 'modules/warehouse.php';

    /**Project Route */
    require_once 'modules/project.php';
    /**Contact Route */
    require_once 'modules/contact.php';
    /** Attibute Route */
    require_once 'modules/attribute.php';
});

Route::get('barcode_print_test', function () {
    $all_products = \App\Models\Product::select('test_bc')->get();
    foreach ($all_products as $key => $v) {
        $text = 'MTS' . $v->test_bc ?? NULL;
        echo \App\Helpers\Query::barcodeGenerator($text);
        echo '<br/>';
        echo str_pad('sam', '4', 'fg');
    }
});
