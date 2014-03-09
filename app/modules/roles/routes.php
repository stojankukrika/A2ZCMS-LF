<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/roles', 'App\Modules\Roles\Controllers\AdminRolesController@getIndex');
	Route::controller('roles', 'App\Modules\Roles\Controllers\AdminRolesController');
});
