<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/', 'App\Modules\Plugins\Controllers\AdminPluginsController@getDashboard');
	Route::get('/plugins', 'App\Modules\Plugins\Controllers\AdminPluginsController@getIndex');
	Route::get('/plugins/reorder', 'App\Modules\Plugins\Controllers\AdminPluginsController@getReorder');
	
});
