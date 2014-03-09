<?php namespace App\Modules\Plugins\Seeds;

use Eloquent, Str;

class PluginsTableSeeder extends \Seeder {

	public function run() {
		// Uncomment the below to wipe the table clean before populating
		// DB::table('settings')->truncate();

		DB::table('settings') -> insert(array( 
											array('name' => 'pages', 
												'title' => 'Pages',
												'function_id' => NULL, 
												'function_grid' => NULL, 
												'can_uninstall' => 1,
												'pluginversion' =>'1.0',
												'active' => 1), 
											array('name' => 'settings', 
												'title' => 'Settings',
												'function_id' => NULL, 
												'function_grid' => NULL, 
												'can_uninstall' => 1,
												'pluginversion' =>'1.0',
												'active' => 1), 
											array('name' => 'users', 
												'title' => 'Users',
												'function_id' => NULL, 
												'function_grid' => NULL, 
												'can_uninstall' => 1,
												'pluginversion' =>'1.0',
												'active' => 1), 
											array('name' => 'roles', 
												'title' => 'Roles',
												'function_id' => NULL, 
												'function_grid' => NULL, 
												'can_uninstall' => 1,
												'pluginversion' =>'1.0',
												'active' => 1), 
											array('name' => 'plugins', 
												'title' => 'Plugins',
												'function_id' => NULL, 
												'function_grid' => NULL, 
												'can_uninstall' => 1,
												'pluginversion' =>'1.0',
												'active' => 1), 
											array('name' => 'adminmenu', 
												'title' => 'Admin menu',
												'function_id' => NULL, 
												'function_grid' => NULL, 
												'can_uninstall' => 1,
												'pluginversion' =>'1.0',
												'active' => 1),
											array('name' => 'menu', 
												'title' => 'Website menu',
												'function_id' => NULL, 
												'function_grid' => NULL, 
												'can_uninstall' => 1,
												'pluginversion' =>'1.0',
												'active' => 1),		
										)
									);
	}

}
