<?php namespace App\Modules\Blogs\Controllers;

use App, View, Session,Auth,URL,Input,Datatables,Redirect,Validator,Str,DB;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Adminmenu\Models\Adminmenu;
use App\Modules\Adminmenu\Models\Adminsubmenu;
use App\Modules\Roles\Models\Permission;
use App\Modules\Pages\Models\PluginFunction;
use App\Modules\Roles\Models\PermissionRole;
use App\Modules\Pages\Models\PagePluginFunction;

class InstallBlogController extends \AdminController {

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
		return View::make('blogs::install/install');
	}
	
	public function postInstall()
	{
		DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."blogs` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `user_id` int(10) unsigned NOT NULL,
					  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `content` text COLLATE utf8_unicode_ci NOT NULL,
					  `voteup` int(10) unsigned NOT NULL DEFAULT '0',
					  `votedown` int(10) unsigned NOT NULL DEFAULT '0',
					  `hits` int(10) unsigned NOT NULL DEFAULT '0',
					  `start_publish` date NOT NULL,
					  `end_publish` date DEFAULT NULL,
					  `resource_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
					  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `blogs_user_id_foreign` (`user_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ");
			
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."blog_blog_categories` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `blog_id` int(10) unsigned NOT NULL,
					  `blog_category_id` int(10) unsigned NOT NULL,
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `blog_blog_categories_blog_id_index` (`blog_id`),
					  KEY `blog_blog_categories_blog_category_id_index` (`blog_category_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."blog_categories` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			
			DB::statement("CREATE TABLE IF NOT EXISTS `".DB::getTablePrefix()."blog_comments` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `user_id` int(10) unsigned NOT NULL,
					  `blog_id` int(10) unsigned NOT NULL,
					  `content` text COLLATE utf8_unicode_ci NOT NULL,
					  `voteup` int(10) unsigned NOT NULL DEFAULT '0',
					  `votedown` int(10) unsigned NOT NULL DEFAULT '0',
					  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `deleted_at` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `blog_comments_user_id_foreign` (`user_id`),
					  KEY `blog_comments_blog_id_foreign` (`blog_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."blogs`
					  ADD CONSTRAINT `blogs_user_id_foreign` FOREIGN KEY (`user_id`) 
					  REFERENCES `".DB::getTablePrefix()."users` (`id`);");
			
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."blog_blog_categories`
					  ADD CONSTRAINT `blog_blog_categories_blog_category_id_foreign` FOREIGN KEY (`blog_category_id`) 
					  REFERENCES `".DB::getTablePrefix()."blog_categories` (`id`) ON DELETE CASCADE,
					  ADD CONSTRAINT `blog_blog_categories_blog_id_foreign` FOREIGN KEY (`blog_id`) 
					  REFERENCES `".DB::getTablePrefix()."blogs` (`id`) ON DELETE CASCADE;");
			
			DB::statement("ALTER TABLE `".DB::getTablePrefix()."blog_comments`
					  ADD CONSTRAINT `blog_comments_blog_id_foreign` FOREIGN KEY (`blog_id`) 
					  REFERENCES `".DB::getTablePrefix()."blogs` (`id`) ON DELETE CASCADE,
					  ADD CONSTRAINT `blog_comments_user_id_foreign` FOREIGN KEY (`user_id`) 
					  REFERENCES `".DB::getTablePrefix()."users` (`id`) ON DELETE CASCADE;");
			
			if (!is_dir(public_path() .'\blog')) {
			    mkdir(public_path() .'\blog', 0777, TRUE);		
			}	
			if (!is_dir(public_path() .'\blog/thumbs')) {
			    mkdir(public_path() .'\blog/thumbs', 0777, TRUE);		
			}
			/*add to plugins*/
			$plugin = new Plugin;
			$plugin -> name = 'blogs';
			$plugin -> title = 'Blog';
			$plugin -> function_id = 'getBlogId';
			$plugin -> function_grid = 'getBlogGroupId';
			$plugin -> can_uninstall = '1';
			$plugin -> pluginversion = '1.0';
			$plugin -> active = '1';
			$plugin -> save();
			$plugin_id = $plugin->id;
			
			/*add to admin root navigation*/			
			$adminmenu = new Adminmenu;
			$adminmenu -> plugin_id = $plugin_id;
			$adminmenu -> icon = 'icon-external-link';
			$adminmenu -> background_color = 'orange';
			$adminmenu -> order = '0';
			$adminmenu -> save();
			$adminmenu_id = $adminmenu->id;			
			
			/*add admin subnavigation*/
			$adminsubmenu = new Adminsubmenu;
			$adminsubmenu -> admin_navigation_id = $adminmenu_id;
			$adminsubmenu -> title = 'Blog categorys';
			$adminsubmenu -> url = 'blogs/blogcategorys';
			$adminsubmenu -> icon = 'icon-rss';
			$adminsubmenu -> order = '1';
			$adminsubmenu -> save();
			
			$adminsubmenu = new Adminsubmenu;
			$adminsubmenu -> admin_navigation_id = $adminmenu_id;
			$adminsubmenu -> title = 'Blog';
			$adminsubmenu -> url = 'blogs';
			$adminsubmenu -> icon = 'icon-book';
			$adminsubmenu -> order = '2';
			$adminsubmenu -> save();
			
			$adminsubmenu = new Adminsubmenu;
			$adminsubmenu -> admin_navigation_id = $adminmenu_id;
			$adminsubmenu -> title = 'Blog comments';
			$adminsubmenu -> url = 'blogs/blogcomments';
			$adminsubmenu -> icon = 'icon-comment-alt';
			$adminsubmenu -> order = '3';
			$adminsubmenu -> save();

			/*add plugin permission*/
			$permission = new Permission;
			$permission -> name = 'manage_blog_categris';
			$permission -> display_name = 'Manage blog categris';
			$permission -> is_admin = '1';
			$permission -> save();
			
			$permission = new Permission;
			$permission -> name = 'manage_blogs';
			$permission -> display_name = 'Manage blogs';
			$permission -> is_admin = '1';
			$permission -> save();
			
			$permission = new Permission;
			$permission -> name = 'post_blog_comment';
			$permission -> display_name = 'Post blog comment';
			$permission -> is_admin = '0';
			$permission -> save();
			
			$permission = new Permission;
			$permission -> name = 'post_blog_vote';
			$permission -> display_name = 'Post blog vote';
			$permission -> is_admin = '0';
			$permission -> save();
			
			/*add plugin function*/
			$permission = new PluginFunction;
			$permission -> title = 'New blogs';
			$permission -> plugin_id = $plugin_id;
			$permission -> function = 'newBlogs';
			$permission -> params = 'sort:asc;order:id;limit:5;';
			$permission -> type = 'sidebar';
			$permission -> save();
			
			$permission = new PluginFunction;
			$permission -> title = 'Display blogs';
			$permission -> plugin_id = $plugin_id;
			$permission -> function = 'showBlogs';
			$permission -> params = 'id;sort;order;limit;';
			$permission -> type = 'content';
			$permission -> save();
			
	}
	
	public function getUninstall()
	{
		return View::make('blogs::install/uninstall');
	}
	
	public function postUninstall()
	{
		/*delete permissions from roles*/
			$permission = Permission::where('name','=','manage_blogs')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();
			
			$permission = Permission::where('name','=','manage_blog_categris')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();			
			
			$permission = Permission::where('name','=','post_blog_comment')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();
			
			$permission = Permission::where('name','=','post_blog_vote')->first();
			PermissionRole::where('id','=',$permission->id)->delete();
			$permission->delete();			
			
			/*delete plugin functions from pages*/
			$plugin_id = Plugin::where('name','=','blogs')->first();
			
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
					
			/*drop blog tables*/
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."blog_comments`");
			
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."blog_blog_categories`");
			
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."blog_categories`");
					
			DB::statement("DROP TABLE IF EXISTS `".DB::getTablePrefix()."blogs`");
			
			if (!is_readable(public_path() .'/blog')) {
			    unlink(public_path() .'/blog', 0777, TRUE);		
			}	
	}

}
