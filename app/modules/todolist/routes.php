<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('todolist', 'App\Modules\Todolist\Controllers\AdminTodolistController@getIndex');
	Route::get('todolist/{id}/change', 'App\Modules\Todolist\Controllers\AdminTodolistController@getChange');
    Route::get('todolist/{id}/edit', 'App\Modules\Todolist\Controllers\AdminTodolistController@getEdit');
    Route::post('todolist/{id}/edit', 'App\Modules\Todolist\Controllers\AdminTodolistController@postEdit');
    Route::get('todolist/{id}/delete', 'App\Modules\Todolist\Controllers\AdminTodolistController@getDelete');
	Route::get('todolist/data', 'App\Modules\Todolist\Controllers\AdminTodolistController@getData');
    Route::post('todolist/{id}/delete', 'App\Modules\Todolist\Controllers\AdminTodolistController@getDelete');
    Route::controller('todolist', 'App\Modules\Todolist\Controllers\AdminTodolistController');	
});
