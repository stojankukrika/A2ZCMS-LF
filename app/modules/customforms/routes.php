<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('customforms', 'App\Modules\Customforms\Controllers\AdminCustomformController@getIndex');
	Route::get('customforms/{id}/edit', 'App\Modules\Customforms\Controllers\AdminCustomformController@getEdit');
    Route::post('customforms/{id}/edit', 'App\Modules\Customforms\Controllers\AdminCustomformController@postEdit');
    Route::get('customforms/{id}/delete', 'App\Modules\Customforms\Controllers\AdminCustomformController@getDelete');
    Route::post('customforms/{id}/delete', 'App\Modules\Customforms\Controllers\AdminCustomformController@getDelete');
	Route::get('customforms/{id}/deleteitem', 'App\Modules\Customforms\Controllers\AdminCustomformController@postDeleteItem');
    Route::post('customforms/{id}/deleteitem', 'App\Modules\Customforms\Controllers\AdminCustomformController@postDeleteItem');
    Route::controller('customforms', 'App\Modules\Customforms\Controllers\AdminCustomformController');
});
