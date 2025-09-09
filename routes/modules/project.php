<?php
Route::group(['key' => 'Project','prefix' => '/project', 'as' => 'project_'], function(){
    Route::get('/manage', ['uses'=>'ProjectController@index', 'title' =>'Manage Project', 'show' => 'Yes', 'position' => 'Top'])->name('index');
    Route::get('/create', ['uses'=>'ProjectController@create', 'title' => 'Add Project'])->name('create');
    Route::post('/store', 'ProjectController@store')->name('store');
    Route::get('/edit/{id}', ['uses'=>'ProjectController@edit', 'title'=>'Edit Project'])->name('edit');
    Route::post('/update', 'ProjectController@update')->name('update');
    Route::delete('/delete/{id}', ['uses'=>'ProjectController@destroy','title'=> 'Delete Project'])->name('destroy');
    Route::get('import-mts-project', ['uses'=>'ProjectController@importMTSProject'])->name('import_mts_project');

});

?>

