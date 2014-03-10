<?php
/*Admin routes*/
Route::pattern('id', '[0-9]+');

Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/roles', 'App\Modules\Roles\Controllers\AdminRolesController@getIndex');
	
	Route::get('/roles/create', 'App\Modules\Roles\Controllers\AdminRolesController@getCreate');
	Route::post('/roles/create', 'App\Modules\Roles\Controllers\AdminRolesController@postCreate');
	
	Route::get('/roles/{id}/edit', 'App\Modules\Roles\Controllers\AdminRolesController@getEdit');
	Route::post('/roles/{id}/edit', 'App\Modules\Roles\Controllers\AdminRolesController@postEdit');
	
	Route::get('/roles/{id}/delete', 'App\Modules\Roles\Controllers\AdminRolesController@getDelete');
	
	Route::controller('roles', 'App\Modules\Roles\Controllers\AdminRolesController');
});
