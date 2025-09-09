<?php
Route::group(['key' => 'Attribute','prefix' => '/attribute', 'as' => 'attribute_'], function(){
    $allAttribute = App\Helpers\Query::getEnumValues('attribute_values','unique_name');
    foreach($allAttribute as $index => $value)  {

        Route::get('/manage/{value}/'.$index, ['uses'=>'AttributeValueController@index', 'param' => [$value], 'title' =>'Manage '.$value, 'show' => 'Yes', 'position' => 'Top'])->name($index.'_index');
        Route::post('/store', ['uses'=>'AttributeValueController@store'])->name('store');

        Route::get('/edit/{value}/{id}'.$index, ['uses'=>'AttributeValueController@edit'])->name($index.'_edit');

        Route::post('/update', ['uses'=>'AttributeValueController@update'])->name('update');

        Route::delete('/destroy/{value}/{id}'.$index, ['uses'=>'AttributeValueController@destroy'])->name($index.'_destroy');
    }
});
?>
