<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/settings', 'App\Modules\Settings\Controllers\AdminSettingsController@getIndex');
	Route::post('/settings', 'App\Modules\Settings\Controllers\AdminSettingsController@postIndex');
	
});
