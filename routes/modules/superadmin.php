<?php

/** Role */
Route::group(['key' => 'Role', 'prefix' => 'role', 'as' => 'role_'], function(){
    Route::get('/manage', ['uses'=>'RoleController@index', 'title' =>'Manage Roles', 'show' => 'Yes', 'position' => 'Left'])->name('index');
    Route::get('/create', ['uses'=>'RoleController@create', 'title' => 'Add', 'show' => 'Yes', 'position' => 'Left'])->name('create');
    Route::post('/store', 'RoleController@store')->name('store');
    Route::get('/edit/{id}', ['uses'=>'RoleController@edit', 'title'=>'Edit'])->name('edit');
    Route::post('/update', 'RoleController@update')->name('update');
    Route::delete('/delete/{id}', ['uses'=>'RoleController@destroy','title'=> 'Delete'])->name('destroy');
});

/** User */

Route::group(['key' => 'User', 'prefix' => 'user', 'as' => 'user_'], function(){
    Route::get('/manage', ['uses'=>'UserController@index', 'title' => 'Manage Users', 'show' => 'Yes', 'position' => 'Left'])->name('index');
    Route::get('/create', ['uses'=>'UserController@create', 'title' => 'Add', 'show' => 'Yes', 'position' => 'Left'])->name('create');
    Route::post('/store', 'UserController@store')->name('store');
    Route::get('/edit/{id}', ['uses' => 'UserController@edit', 'title' =>'Edit'])->name('edit');
    Route::get('/edit-profile/{id}', ['uses' => 'UserController@editprofile', 'title' =>'Edit Profile'])->name('edit_profile');
    Route::post('/update', 'UserController@update')->name('update');
    Route::post('/change-password', 'UserController@changePassword')->name('change_password');
    Route::delete('/delete/{id}', ['uses'=>'UserController@destroy', 'title' => 'Delete'])->name('destroy');


    //Api
    Route::get('/api/getuser', 'UserController@apiGetUser')->name('api_getuser');
});

/** Routelist */

Route::group(['key' => 'Routelist', 'prefix' => 'routelist', 'as' => 'routelist_'], function(){
    Route::get('/manage', ['uses'=>'RoutelistController@index','title' => 'Manage Route', 'show' => 'Yes', 'position' => 'Left'])->name('index');
    Route::get('/create', ['uses'=>'RoutelistController@create', 'title' => 'Add', 'show' => 'Yes', 'position' => 'Left'])->name('create');
    Route::post('/store', 'RoutelistController@store')->name('store');
    Route::get('/edit/{id}', ['uses'=>'RoutelistController@edit', 'title' => 'Edit'])->name('edit');
    Route::post('/update', 'RoutelistController@update')->name('update');
    Route::delete('/delete/{id}', ['uses'=>'RoutelistController@destroy','title' => 'Delete'])->name('destroy');


    //Api
    Route::get('/api/get', 'RoutelistController@apiGet')->name('api_get');
});

/**
 * Translate
 */
Route::group(['prefix' => 'translate', 'as' => 'translate_'], function(){
    Route::post('/store-or-update', 'TranslateController@storeOrUpdate')->name('store_or_update');
});
