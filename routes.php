<?php 

Route::group(['middleware' => ['web', Admin::middleware()], 'prefix' => 'admin'], function () {
    Route::get('/', 'WhiteCube\Admin\Controllers\AdminController@index')->name('kabas.admin');
    Route::get('pages/{route}', 'WhiteCube\Admin\Controllers\PageController@show')->name('kabas.admin.page');
    Route::post('pages', 'WhiteCube\Admin\Controllers\PageController@process')->name('kabas.admin.page.submit');
    Route::get('models/{file}', 'WhiteCube\Admin\Controllers\ModelController@list')->name('kabas.admin.model');
    Route::get('models/{file}/add', 'WhiteCube\Admin\Controllers\ModelController@add')->name('kabas.admin.model.add');
    Route::post('models/{file}/create', 'WhiteCube\Admin\Controllers\ModelController@create')->name('kabas.admin.model.create');
    Route::get('models/{file}/{id}', 'WhiteCube\Admin\Controllers\ModelController@show')->name('kabas.admin.model.item');
    Route::post('models', 'WhiteCube\Admin\Controllers\ModelController@process')->name('kabas.admin.model.submit');
    Route::post('models/delete/{file}/{id}', 'WhiteCube\Admin\Controllers\ModelController@del')->name('kabas.admin.model.delete');
    Route::delete('models/{file}/{id}', 'WhiteCube\Admin\Controllers\ModelController@destroy')->name('kabas.admin.model.destroy');
    Route::get('customs/{file}/{params?}', 'WhiteCube\Admin\Controllers\CustomController@show')->where('params', '(.*)')->name('kabas.admin.custom');
    Route::post('customs/{file}/{params?}', 'WhiteCube\Admin\Controllers\CustomController@post')->where('params', '(.*)')->name('kabas.admin.custom.post');
});
