<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/todolist', 'App\Modules\Todolist\Controllers\AdminTodolistController@getIndex');
	
});
