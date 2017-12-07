<?php 

Route::group(['middleware' => ['web', Admin::middleware()], 'prefix' => 'admin'], function() {
    Route::get('/', 'WhiteCube\Admin\Controllers\AdminController@index')->name('kabas.admin');
    Route::get('pages/{file}', 'WhiteCube\Admin\Controllers\PageController@show')->name('kabas.admin.page');
    Route::post('pages', 'WhiteCube\Admin\Controllers\PageController@process')->name('kabas.admin.page.submit');
    Route::get('models/{file}', 'WhiteCube\Admin\Controllers\ModelController@list')->name('kabas.admin.model');
    Route::get('models/{file}/{id}', 'WhiteCube\Admin\Controllers\ModelController@show')->name('kabas.admin.model.item');
    Route::post('models', 'WhiteCube\Admin\Controllers\ModelController@process')->name('kabas.admin.model.submit');
});