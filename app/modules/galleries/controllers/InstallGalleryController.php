<?php namespace App\Modules\Galleries\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,Str,DB;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Adminmenu\Models\Adminmenu;
use App\Modules\Adminmenu\Models\Adminsubmenu;
use App\Modules\Roles\Models\Permission;
use App\Modules\Pages\Models\PluginFunction;
use App\Modules\Roles\Models\PermissionRole;
use App\Modules\Pages\Models\PagePluginFunction;

class InstallGalleryController extends \AdminController {

	function __construct(\AdminController $admin)
	{
		parent::__construct();
		if (!array_key_exists('manage_plugins',$admin->roles)){
			header('Location: '. $_SERVER['HTTP_REFERER']);
			exit ;
		}
	}
	public function getInstall()
	{
		return View::make('galleries::install/install');
	}
	
	public function postInstall()
	{
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."galleries` (
						  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `user_id` int(10) unsigned NOT NULL,
						  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
						  `hits` int(10) unsigned NOT NULL DEFAULT '0',
						   `voteup` int(10) NOT NULL DEFAULT '0',
						   `votedown` int(10) NOT NULL DEFAULT '0',
						  `folderid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
						  `start_publish` date NOT NULL,
						  `end_publish` date DEFAULT NULL,
						  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
						  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
						  `deleted_at` timestamp NULL DEFAULT NULL,
						  PRIMARY KEY (`id`),
						  KEY `gallery_user_id_foreign` (`user_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."gallery_images` (
						  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `gallery_id` int(10) unsigned NOT NULL,
						  `user_id` int(10) unsigned NOT NULL,
						  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
						  `voteup` int(10) unsigned NOT NULL DEFAULT '0',
						  `votedown` int(10) unsigned NOT NULL DEFAULT '0',
						  `hits` int(10) unsigned NOT NULL DEFAULT '0',
						  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
						  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
						  `deleted_at` timestamp NULL DEFAULT NULL,
						  PRIMARY KEY (`id`),
						  KEY `gallery_images_gallery_id_foreign` (`gallery_id`),
						  KEY `gallery_images_user_id_foreign` (`user_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
						
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."gallery_images_comments` (
						  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `user_id` int(10) unsigned NOT NULL,
						  `gallery_id` int(10) unsigned NOT NULL,
						  `gallery_image_id` int(10) unsigned NOT NULL,
						  `content` text COLLATE utf8_unicode_ci NOT NULL,
						  `voteup` int(10) unsigned NOT NULL DEFAULT '0',
						  `votedown` int(10) unsigned NOT NULL DEFAULT '0',
						  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
						  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
						  `deleted_at` timestamp NULL DEFAULT NULL,
						  PRIMARY KEY (`id`),
						  KEY `gallery_images_comments_user_id_foreign` (`user_id`),
						  KEY `gallery_images_comments_gallery_id_foreign` (`gallery_id`),
						  KEY `gallery_images_comments_gallery_image_id_foreign` (`gallery_image_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
						
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."galleries`
						  ADD CONSTRAINT `gallery_user_id_foreign` FOREIGN KEY (`user_id`) 
						  REFERENCES `".DB::getTablePrefix()."users` (`id`);");
						  
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."gallery_images`
						  ADD CONSTRAINT `gallery_images_user_id_foreign` FOREIGN KEY (`user_id`) 
						  REFERENCES `".DB::getTablePrefix()."users` (`id`) ON DELETE CASCADE,
						  ADD CONSTRAINT `gallery_images_gallery_id_foreign` FOREIGN KEY (`gallery_id`) 
						  REFERENCES `".DB::getTablePrefix()."galleries` (`id`) ON DELETE CASCADE;");
						  
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."gallery_images_comments`
						  ADD CONSTRAINT `gallery_images_comments_gallery_image_id_foreign` 
						  FOREIGN KEY (`gallery_image_id`) REFERENCES `".DB::getTablePrefix()."gallery_images` (`id`) ON DELETE CASCADE,
						  ADD CONSTRAINT `gallery_images_comments_gallery_id_foreign` 
						  FOREIGN KEY (`gallery_id`) REFERENCES `".DB::getTablePrefix()."galleries` (`id`) ON DELETE CASCADE,
						  ADD CONSTRAINT `gallery_images_comments_user_id_foreign` 
						  FOREIGN KEY (`user_id`) REFERENCES `".DB::getTablePrefix()."users` (`id`) ON DELETE CASCADE;");
			
			if (!is_dir(public_path() .'\gallery')) {
			    mkdir(public_path() .'\gallery', 0777, TRUE);		
			}	
			/*add to plugins*/
			$plugin = new Plugin;
			$plugin -> name = 'galleries';
			$plugin -> title = 'Gallery';
			$plugin -> function_id = 'getGalleryId';
			$plugin -> function_grid = NULL;
			$plugin -> can_uninstall = '1';
			$plugin -> pluginversion = '1.0';
			$plugin -> active = '1';
			$plugin -> save();
			$plugin_id = $plugin->id;
			
			/*add to admin root navigation*/
			$adminmenu = new Adminmenu;
			$adminmenu -> plugin_id = $plugin_id;
			$adminmenu -> icon = 'icon-camera';
			$adminmenu -> background_color = 'blue';
			$adminmenu -> order = '0';
			$adminmenu -> save();
			$adminmenu_id = $adminmenu->id;
			
			/*add to admin subnavigation*/
			$adminsubmenu = new Adminsubmenu;
			$adminsubmenu -> admin_navigation_id = $adminmenu_id;
			$adminsubmenu -> title = 'Gallery images';
			$adminsubmenu -> url = 'galleries/galleryimages';
			$adminsubmenu -> icon = 'icon-rss';
			$adminsubmenu -> order = '1';
			$adminsubmenu -> save();
			
			$adminsubmenu = new Adminsubmenu;
			$adminsubmenu -> admin_navigation_id = $adminmenu_id;
			$adminsubmenu -> title = 'Galleries';
			$adminsubmenu -> url = 'galleries';
			$adminsubmenu -> icon = 'icon-camera-retro';
			$adminsubmenu -> order = '1';
			$adminsubmenu -> save();
			
			$adminsubmenu = new Adminsubmenu;
			$adminsubmenu -> admin_navigation_id = $adminmenu_id;
			$adminsubmenu -> title = 'Gallery comments';
			$adminsubmenu -> url = 'galleries/galleryimagecomments';
			$adminsubmenu -> icon = 'icon-comments-alt';
			$adminsubmenu -> order = '1';
			$adminsubmenu -> save();

			/*add plugin permission*/
			$permission = new Permission;
			$permission -> name = 'manage_galleries';
			$permission -> display_name = 'Manage galleries';
			$permission -> is_admin = '1';
			$permission -> save();
			
			$permission = new Permission;
			$permission -> name = 'manage_gallery_images';
			$permission -> display_name = 'Manage gallery images';
			$permission -> is_admin = '1';
			$permission -> save();
			
			$permission = new Permission;
			$permission -> name = 'manage_gallery_imagecomments';
			$permission -> display_name = 'Manage gallery image comments';
			$permission -> is_admin = '1';
			$permission -> save();
			
			$permission = new Permission;
			$permission -> name = 'post_gallery_comment';
			$permission -> display_name = 'Post gallery comment';
			$permission -> is_admin = '0';
			$permission -> save();
			
			$permission = new Permission;
			$permission -> name = 'post_image_vote';
			$permission -> display_name = 'Post image vote';
			$permission -> is_admin = '0';
			$permission -> save();
			
			/*add plugin function*/
			$permission = new PluginFunction;
			$permission -> title = 'New gallerys';
			$permission -> plugin_id = $plugin_id;
			$permission -> function = 'newGallery';
			$permission -> params = 'sort:asc;order:id;limit:5;';
			$permission -> type = 'sidebar';
			$permission -> save();
			
			$permission = new PluginFunction;
			$permission -> title = 'Galleries';
			$permission -> plugin_id = $plugin_id;
			$permission -> function = 'showGalleries';
			$permission -> params = 'id;sort;order;limit;';
			$permission -> type = 'content';
			$permission -> save();
	}
	
	public function getUninstall()
	{
		return View::make('galleries::install/uninstall');
	}
	
	public function postUninstall()
	{
			/*delete permissions from roles*/	
			$permission = Permission::where('name','=','manage_gallery_images')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();
			
			$permission = Permission::where('name','=','manage_gallery_imagecomments')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();			
			
			$permission = Permission::where('name','=','post_gallery_comment')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();
			
			$permission = Permission::where('name','=','post_image_vote')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();
			
			$permission = Permission::where('name','=','manage_galleries')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();
			
			/*delete plugin functions from pages*/
			$plugin_id = Plugin::where('name','=','galleries')->first();
			
			$plugins = PluginFunction::where('plugin_id','=',$plugin_id->id)->get();
						
			foreach ($plugins as $item) {
					PagePluginFunction::where('plugin_function_id','=',$item->id)->delete();
					PluginFunction::where('id','=',$item->id)->delete();
			}		
			
			/*delete admin navigation*/			
			$navigation = Adminmenu::where('plugin_id','=',$plugin_id->id)->first();			
			Adminsubmenu::where('admin_navigation_id','=',$navigation->id)->delete();
			$navigation->delete();		
			
			/*delete plugin*/
			$plugin_id->delete(); 
					
			/*drop gallery tables*/
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."gallery_images_comments`");
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."gallery_images`");
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."galleries`");
			
			
			if (!is_readable(public_path() .'\gallery')) {
			    unlink(public_path() .'\gallery', 0777, TRUE);		
			}	
	}	
}
