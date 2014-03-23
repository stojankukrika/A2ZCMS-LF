<?php
Route::pattern('id', '[0-9]+');
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('customforms', 'App\Modules\Customforms\Controllers\AdminCustomformController@getIndex');	
		
	Route::get('customforms/install', 'App\Modules\Customforms\Controllers\InstallCustomformController@getInstall');
   	Route::post('customforms/install', 'App\Modules\Customforms\Controllers\InstallCustomformController@postInstall');
   	Route::get('customforms/uninstall', 'App\Modules\Customforms\Controllers\InstallCustomformController@getUninstall');
   	Route::post('customforms/uninstall', 'App\Modules\Customforms\Controllers\InstallCustomformController@postUninstall');
	
	Route::get('customforms/{id}/edit', 'App\Modules\Customforms\Controllers\AdminCustomformController@getEdit');
    Route::post('customforms/{id}/edit', 'App\Modules\Customforms\Controllers\AdminCustomformController@postEdit');
    Route::get('customforms/{id}/delete', 'App\Modules\Customforms\Controllers\AdminCustomformController@getDelete');
    Route::post('customforms/{id}/delete', 'App\Modules\Customforms\Controllers\AdminCustomformController@getDelete');
	Route::get('customforms/{id}/deleteitem', 'App\Modules\Customforms\Controllers\AdminCustomformController@postDeleteItem');
    Route::post('customforms/{id}/deleteitem', 'App\Modules\Customforms\Controllers\AdminCustomformController@postDeleteItem');
    Route::controller('customforms', 'App\Modules\Customforms\Controllers\AdminCustomformController');
});
