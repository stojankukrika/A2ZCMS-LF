<?php
Route::pattern('id', '[0-9]+');

/*Site route*/

Route::get('page/{id}', 'App\Modules\Pages\Controllers\PagesController@getView');
Route::post('page/{id}', 'App\Modules\Pages\Controllers\PagesController@postView');
Route::get('', 'App\Modules\Pages\Controllers\PagesController@getView');
Route::controller('page', 'App\Modules\Pages\Controllers\PagesController');

/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	 # Navigation Management
    Route::get('pages/navigation/{id}/edit', 'App\Modules\Pages\Controllers\AdminNavigationController@getEdit');
    Route::post('pages/navigation/{id}/edit', 'App\Modules\Pages\Controllers\AdminNavigationController@postEdit');
    Route::get('pages/navigation/{id}/delete', 'App\Modules\Pages\Controllers\AdminNavigationController@getDelete');
    Route::post('pages/navigation/{id}/delete', 'App\Modules\Pages\Controllers\AdminNavigationController@getDelete');
    Route::controller('pages/navigation', 'App\Modules\Pages\Controllers\AdminNavigationController');
	
	# Navigation Group Management
    Route::get('pages/navigationgroups/{id}/edit', 'App\Modules\Pages\Controllers\AdminNavigationGroupController@getEdit');
    Route::post('pages/navigationgroups/{id}/edit', 'App\Modules\Pages\Controllers\AdminNavigationGroupController@postEdit');
    Route::get('pages/navigationgroups/{id}/delete', 'App\Modules\Pages\Controllers\AdminNavigationGroupController@getDelete');
    Route::post('pages/navigationgroups/{id}/delete', 'App\Modules\Pages\Controllers\AdminNavigationGroupController@getDelete');
    Route::controller('pages/navigationgroups', 'App\Modules\Pages\Controllers\AdminNavigationGroupController');

   
	
	# Pages Management
	Route::get('pages/{id}/visible', 'App\Modules\Pages\Controllers\AdminPageController@getVisible');
	Route::get('pages', 'App\Modules\Pages\Controllers\AdminPageController@getIndex');
	Route::get('pages/create', 'App\Modules\Pages\Controllers\AdminPageController@getCreate');
	Route::post('pages/create', 'App\Modules\Pages\Controllers\AdminPageController@postCreate');
	Route::get('pages/{id}/edit', 'App\Modules\Pages\Controllers\AdminPageController@getEdit');
    Route::post('pages/{id}/edit', 'App\Modules\Pages\Controllers\AdminPageController@postEdit');
    Route::get('pages/{id}/delete', 'App\Modules\Pages\Controllers\AdminPageController@getDelete');
    Route::post('pages/{id}/delete', 'App\Modules\Pages\Controllers\AdminPageController@getDelete');
    Route::controller('pages', 'App\Modules\Pages\Controllers\AdminPageController');
	
});
