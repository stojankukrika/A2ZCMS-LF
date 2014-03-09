<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/galleries', 'App\Modules\Customforms\Controllers\AdminCustomformsController@getIndex');
	
});
