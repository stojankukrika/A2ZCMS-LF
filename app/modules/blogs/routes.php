<?php
Route::pattern('id', '[0-9]+');
/*Admin routes*/
Route::group(array('prefix' => 'admin', 'before' => 'auth|detectLang'), function()
{
	# Blog Comment Management
    Route::get('blogs/blogcomments/{id}/commentsforblog', 'App\Modules\Blogs\Controllers\AdminBlogCommentController@getCommentsForBlog');
    Route::get('blogs/blogcomments/{id}/edit', 'App\Modules\Blogs\Controllers\AdminBlogCommentController@getEdit');
    Route::post('blogs/blogcomments/{id}/edit', 'App\Modules\Blogs\Controllers\AdminBlogCommentController@postEdit');
    Route::get('blogs/blogcomments/{id}/delete', 'App\Modules\Blogs\Controllers\AdminBlogCommentController@getDelete');
    Route::post('blogs/blogcomments/{id}/delete', 'App\Modules\Blogs\Controllers\AdminBlogCommentController@getDelete');
    Route::get('blogs/blogcomments/data', 'App\Modules\Blogs\Controllers\AdminBlogCommentController@getData');
    Route::controller('blogs/blogcomments', 'App\Modules\Blogs\Controllers\AdminBlogCommentController');
	
	 # Blog Category Management
    Route::get('blogs/blogcategorys/{id}/edit', 'App\Modules\Blogs\Controllers\AdminBlogCategoryController@getEdit');
    Route::post('blogs/blogcategorys/{id}/edit', 'App\Modules\Blogs\Controllers\AdminBlogCategoryController@postEdit');
    Route::get('blogs/blogcategorys/{id}/delete', 'App\Modules\Blogs\Controllers\AdminBlogCategoryController@getDelete');
    Route::post('blogs/blogcategorys/{id}/delete', 'App\Modules\Blogs\Controllers\AdminBlogCategoryController@getDelete');
    Route::get('blogs/blogcategorys/data', 'App\Modules\Blogs\Controllers\AdminBlogCategoryController@getData');
    Route::controller('blogs/blogcategorys', 'App\Modules\Blogs\Controllers\AdminBlogCategoryController');

    # Blog Management
    Route::get('blogs/install', 'App\Modules\Blogs\Controllers\InstallBlogController@getInstall');
   	Route::post('blogs/install', 'App\Modules\Blogs\Controllers\InstallBlogController@postInstall');
   	Route::get('blogs/uninstall', 'App\Modules\Blogs\Controllers\InstallBlogController@getUninstall');
   	Route::post('blogs/uninstall', 'App\Modules\Blogs\Controllers\InstallBlogController@postUninstall');
	
	Route::get('blogs/{id}/edit', 'App\Modules\Blogs\Controllers\AdminBlogController@getEdit');
    Route::post('blogs/{id}/edit', 'App\Modules\Blogs\Controllers\AdminBlogController@postEdit');
    Route::get('blogs/{id}/delete', 'App\Modules\Blogs\Controllers\AdminBlogController@getDelete');
    Route::post('blogs/{id}/delete', 'App\Modules\Blogs\Controllers\AdminBlogController@getDelete');
    Route::get('blogs/data', 'App\Modules\Blogs\Controllers\AdminBlogController@getData');
	Route::get('blogs', 'App\Modules\Blogs\Controllers\AdminBlogsController@getIndex');
	
    Route::controller('blogs', 'App\Modules\Blogs\Controllers\AdminBlogController');
	
});
