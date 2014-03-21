<?php

Route::group(array('prefix' => 'install'), function()
{
	Route::get('', 'App\Modules\Install\Controllers\InstallController@getIndex');	
	Route::controller('install', 'App\Modules\Install\Controllers\InstallController');

});
