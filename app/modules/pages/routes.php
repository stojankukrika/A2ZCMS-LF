<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/pages', 'App\Modules\Pages\Controllers\AdminPagesController@getIndex');
	
});
