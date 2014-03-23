<?php
Route::pattern('id', '[0-9]+');
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('todolist', 'App\Modules\Todolist\Controllers\AdminTodolistController@getIndex');
		
	Route::get('todolist/install', 'App\Modules\Todolist\Controllers\InstallTodolistController@getInstall');
   	Route::post('todolist/install', 'App\Modules\Todolist\Controllers\InstallTodolistController@postInstall');
   	Route::get('todolist/uninstall', 'App\Modules\Todolist\Controllers\InstallTodolistController@getUninstall');
   	Route::post('todolist/uninstall', 'App\Modules\Todolist\Controllers\InstallTodolistController@postUninstall');
	
	Route::get('todolist/{id}/change', 'App\Modules\Todolist\Controllers\AdminTodolistController@getChange');
    Route::get('todolist/{id}/edit', 'App\Modules\Todolist\Controllers\AdminTodolistController@getEdit');
    Route::post('todolist/{id}/edit', 'App\Modules\Todolist\Controllers\AdminTodolistController@postEdit');
    Route::get('todolist/{id}/delete', 'App\Modules\Todolist\Controllers\AdminTodolistController@getDelete');
	Route::get('todolist/data', 'App\Modules\Todolist\Controllers\AdminTodolistController@getData');
    Route::post('todolist/{id}/delete', 'App\Modules\Todolist\Controllers\AdminTodolistController@getDelete');
    Route::controller('todolist', 'App\Modules\Todolist\Controllers\AdminTodolistController');	
});
