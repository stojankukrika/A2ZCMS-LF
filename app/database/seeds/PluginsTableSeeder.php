<?php

class PluginsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('plugins')->truncate();

		// Uncomment the below to run the seeder
		 DB::table('plugins')->insert(array( 
					array('name' => 'pages', 
						'title' => 'Pages', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),					
					array('name' => 'settings', 
						'title' => 'Settings', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'users', 
						'title' => 'Users', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'roles', 
						'title' => 'Roles', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'plugins', 
						'title' => 'Plugins', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'adminmenu', 
						'title' => 'Admin menu', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ), 
					array('name' => 'menu', 
						'title' => 'Website menu', 
						'function_id' => NULL,
						'function_grid' => NULL,
						'can_uninstall' => '0',
						'pluginversion' => '1.0',
						'active' => '1',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, )	
						
				));
	}

}
