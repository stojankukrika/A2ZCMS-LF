<?php
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	Route::get('/galleries', 'App\Modules\Galleries\Controllers\AdminGalleriesController@getIndex');
	Route::get('/galleries/{id}/imagesforgallery', 'App\Modules\Galleries\Controllers\AdminGalleryController@getImagesForGallery');
    Route::get('/galleries/{id}/edit', 'App\Modules\Galleries\Controllers\AdminGalleryController@getEdit');
    Route::post('/galleries/{id}/edit', 'App\Modules\Galleries\Controllers\AdminGalleryController@postEdit');
    Route::get('/galleries/{id}/delete', 'App\Modules\Galleries\Controllers\AdminGalleryController@getDelete');
    Route::post('/galleries/{id}/delete', 'App\Modules\Galleries\Controllers\AdminGalleryController@getDelete');
	Route::get('/galleries/{id}/upload', 'App\Modules\Galleries\Controllers\AdminGalleryController@getUpload');
    Route::post('/galleries/{id}/upload', 'App\Modules\Galleries\Controllers\AdminGalleryController@postUpload');		
    Route::controller('/galleries', 'App\Modules\Galleries\Controllers\AdminGalleryController');
});
