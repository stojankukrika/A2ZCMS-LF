<?php
Route::pattern('id', '[0-9]+');
Route::pattern('token', '[0-9a-z-]+');


/*Site routes*/
Route::group(array('prefix' => 'users'), function()
{
	 Route::group(array('before' => 'auth'), function()
	{
	    //:: User Account Routes ::
		Route::post('/{id}/edit', 'App\Modules\Users\Controllers\UserController@postEdit');
		//User messages
		Route::get('/messages', 'App\Modules\Users\Controllers\UserMessagesController@getIndex');
		Route::get('/messages/{id}/read', 'App\Modules\Users\Controllers\UserMessagesController@getRead');
		Route::post('/messages/sendmessage', 'App\Modules\Users\Controllers\UserMessagesController@postSendmessage');
		
	});
	// User reset routes
	Route::get('/reset/{token}', 'App\Modules\Users\Controllers\UserController@getReset');
	// User password reset
	Route::post('/reset/{token}', 'App\Modules\Users\Controllers\UserController@postReset');
	
	//:: User Account Routes ::
	Route::get('/login', 'App\Modules\Users\Controllers\UserController@getLogin');
	
	//:: User Account Routes ::
	Route::post('/login', 'App\Modules\Users\Controllers\UserController@postLogin');
	
	//:: User Account Routes ::
	Route::get('/forgot', 'App\Modules\Users\Controllers\UserController@getForgot');
	Route::post('/forgot', 'App\Modules\Users\Controllers\UserController@postForgot');
	
	Route::get('/create', 'App\Modules\Users\Controllers\UserController@getCreate');
	Route::post('/create', 'App\Modules\Users\Controllers\UserController@postCreate');
	
});

/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('users/profile', 'App\Modules\Users\Controllers\AdminUserController@getProfileEdit');
	Route::post('users/profile', 'App\Modules\Users\Controllers\AdminUserController@postProfileEdit');
	Route::get('users/index', 'App\Modules\Users\Controllers\AdminUserController@getIndex');
	
});
