<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/blogs', 'App\Modules\Blogs\Controllers\AdminBlogsController@getIndex');
	
});
