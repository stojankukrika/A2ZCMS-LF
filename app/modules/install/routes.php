<?php
Route::get('install', 'App\Modules\Install\Controllers\InstallController@getIndex');	
Route::controller('install', 'App\Modules\Install\Controllers\InstallController');
