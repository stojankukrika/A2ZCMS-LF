<?php
Route::get('install/step2', 'App\Modules\Install\Controllers\InstallController@getStep2');	
Route::post('install/step2', 'App\Modules\Install\Controllers\InstallController@postStep2');

Route::get('install/step3', 'App\Modules\Install\Controllers\InstallController@getStep3');	
Route::post('install/step3', 'App\Modules\Install\Controllers\InstallController@postStep3');

Route::get('install/step4', 'App\Modules\Install\Controllers\InstallController@getStep4');	
Route::post('install/step4', 'App\Modules\Install\Controllers\InstallController@postStep4');

Route::get('install/step5', 'App\Modules\Install\Controllers\InstallController@getStep5');	
Route::post('install/step5', 'App\Modules\Install\Controllers\InstallController@postStep5');

Route::get('install', 'App\Modules\Install\Controllers\InstallController@getIndex');	
Route::post('install', 'App\Modules\Install\Controllers\InstallController@postIndex');