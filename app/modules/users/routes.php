<?php
Route::pattern('id', '[0-9]+');
Route::pattern('token', '[0-9a-z-]+');

/*Site routes*/
Route::group(array('prefix' => 'users'), function()
{
	 Route::group(array('before' => 'auth'), function()
	{
	    //:: User Account Routes ::
		Route::post('{id}/edit', 'App\Modules\Users\Controllers\UserController@postEdit');
		//User messages
		Route::get('messages', 'App\Modules\Users\Controllers\MessagesController@getIndex');
		Route::get('messages/{id}/read', 'App\Modules\Users\Controllers\MessagesController@getRead');
		Route::post('messages/sendmessage', 'App\Modules\Users\Controllers\MessagesController@postSendmessage');
		
	});
	// User reset routes
	Route::get('reset/{token}', 'App\Modules\Users\Controllers\UserController@getReset');
	// User password reset
	Route::post('reset/{token}', 'App\Modules\Users\Controllers\UserController@postReset');
	
	//:: User Account Routes ::
	Route::get('login', 'App\Modules\Users\Controllers\UserController@getLogin');
	
	//:: User Account Routes ::
	Route::post('login', 'App\Modules\Users\Controllers\UserController@postLogin');
	
	//:: User Account Routes ::
	Route::get('forgot', 'App\Modules\Users\Controllers\UserController@getForgot');
	Route::post('forgot', 'App\Modules\Users\Controllers\UserController@postForgot');
	
	Route::get('create', 'App\Modules\Users\Controllers\UserController@getCreate');
	Route::post('create', 'App\Modules\Users\Controllers\UserController@postCreate');
	
	Route::get('logout', 'App\Modules\Users\Controllers\UserController@getLogout');
	
});

/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('users/profile', 'App\Modules\Users\Controllers\AdminUserController@getProfileEdit');
	Route::post('users/profile', 'App\Modules\Users\Controllers\AdminUserController@postProfileEdit');
	
	Route::get('users/create', 'App\Modules\Users\Controllers\AdminUserController@getCreate');
	Route::post('users/create', 'App\Modules\Users\Controllers\AdminUserController@postCreate');
	
	Route::get('users/{id}/edit', 'App\Modules\Users\Controllers\AdminUserController@getEdit');
	Route::post('users/{id}/edit', 'App\Modules\Users\Controllers\AdminUserController@postEdit');
	
	Route::get('users/{id}/delete', 'App\Modules\Users\Controllers\AdminUserController@getDelete');
	
	Route::get('users/{id}/usersforrole', 'App\Modules\Users\Controllers\AdminUserController@getUsersForRole');
	Route::get('users/{id}/usershistory', 'App\Modules\Users\Controllers\AdminUserController@getHistory');
	
	Route::controller('users', 'App\Modules\Users\Controllers\AdminUserController');
	
});
