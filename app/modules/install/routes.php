<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/galleries', 'App\Modules\Galleries\Controllers\AdminGalleriesController@getIndex');
	
});
