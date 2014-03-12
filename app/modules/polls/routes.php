<?php
/*Admin routes*/
Route::pattern('id', '[0-9]+');

Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('polls', 'App\Modules\Polls\Controllers\AdminPollController@getIndex');
	Route::get('polls/{id}/edit', 'App\Modules\Polls\Controllers\AdminPollController@getEdit');
    Route::post('polls/{id}/edit', 'App\Modules\Polls\Controllers\AdminPollController@postEdit');
    Route::get('polls/{id}/delete', 'App\Modules\Polls\Controllers\AdminPollController@getDelete');
    Route::post('polls/{id}/delete', 'App\Modules\Polls\Controllers\AdminPollController@getDelete');
	Route::get('polls/{id}/deleteitem', 'App\Modules\Polls\Controllers\AdminPollController@postDeleteItem');
    Route::post('polls/{id}/deleteitem', 'App\Modules\Polls\Controllers\AdminPollController@postDeleteItem');
    Route::controller('polls', 'App\Modules\Polls\Controllers\AdminPollController');
	
});
