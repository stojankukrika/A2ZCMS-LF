<?php
Route::pattern('id', '[0-9]+');

/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	 # Gallery Comment Management
    Route::get('galleries/galleryimagecomments/{id}/commentsforgallery', 'App\Modules\Galleries\Controllers\AdminGalleryImageCommentController@getCommentsforgallery');
    Route::get('galleries/galleryimagecomments/{id}/edit', 'App\Modules\Galleries\Controllers\AdminGalleryImageCommentController@getEdit');
    Route::post('galleries/galleryimagecomments/{id}/edit', 'App\Modules\Galleries\Controllers\AdminGalleryImageCommentController@postEdit');
    Route::get('galleries/galleryimagecomments/{id}/delete', 'App\Modules\Galleries\Controllers\AdminGalleryImageCommentController@getDelete');
    Route::post('galleries/galleryimagecomments/{id}/delete', 'App\Modules\Galleries\Controllers\AdminGalleryImageCommentController@getDelete');
    Route::controller('galleries/galleryimagecomments', 'App\Modules\Galleries\Controllers\AdminGalleryImageCommentController');
	
	 # Gallery Images Management
	Route::get('galleries/galleryimages', 'App\Modules\Galleries\Controllers\AdminGalleryImageController@getIndex');
    Route::get('galleries/galleryimages/{id}/imageforgallery', 'App\Modules\Galleries\Controllers\AdminGalleryImageController@getImageforgallery');
	Route::get('galleries/galleryimages/{id}/delete', 'App\Modules\Galleries\Controllers\AdminGalleryImageController@postDelete');
	Route::controller('galleries/galleryimages', 'App\Modules\Galleries\Controllers\AdminGalleryImageController');
		
	# Gallery	
	Route::get('galleries', 'App\Modules\Galleries\Controllers\AdminGalleryController@getIndex');
	Route::get('galleries/{id}/imagesforgallery', 'App\Modules\Galleries\Controllers\AdminGalleryController@getImagesForGallery');
    Route::get('galleries/{id}/edit', 'App\Modules\Galleries\Controllers\AdminGalleryController@getEdit');
    Route::post('galleries/{id}/edit', 'App\Modules\Galleries\Controllers\AdminGalleryController@postEdit');
    Route::get('galleries/{id}/delete', 'App\Modules\Galleries\Controllers\AdminGalleryController@getDelete');
    Route::post('galleries/{id}/delete', 'App\Modules\Galleries\Controllers\AdminGalleryController@getDelete');
	Route::get('galleries/{id}/upload', 'App\Modules\Galleries\Controllers\AdminGalleryController@getUpload');
    Route::post('galleries/{id}/upload', 'App\Modules\Galleries\Controllers\AdminGalleryController@postUpload');		
    Route::controller('galleries', 'App\Modules\Galleries\Controllers\AdminGalleryController');
	
		
});
