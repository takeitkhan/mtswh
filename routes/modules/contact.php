<?php 
Route::group(['key' => 'Contact','prefix' => '/contact', 'as' => 'contact_'], function(){    
    Route::get('/manage', ['uses'=>'ContactController@index', 'title' =>'Manage Contact', 'show' => 'Yes', 'position' => 'Top'])->name('index');
    Route::get('/create', ['uses'=>'ContactController@create', 'title' => 'Add Contact'])->name('create');
    Route::post('/store', 'ContactController@store')->name('store');
    Route::get('/edit/{id}', ['uses'=>'ContactController@edit', 'title'=>'Edit Contact'])->name('edit');
    Route::post('/update', 'ContactController@update')->name('update');
    Route::delete('/delete/{id}', ['uses'=>'ContactController@destroy','title'=> 'Delete Contact'])->name('destroy');

    Route::get('/api/sources', ['uses'=>'ContactController@apiSources'])->name('api_source');
});