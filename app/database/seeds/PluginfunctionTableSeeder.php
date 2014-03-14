<?php

class PluginfunctionTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('pluginfunctions')->truncate();		

		// Uncomment the below to run the seeder
		 DB::table('plugin_functions')->insert(array( 
					array('title' => 'Login form', 
						'plugin_id' => 0,
						'function'=>'login_partial',
						'params'=>'',
						'type' => 'sidebar',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),					
					array('title' => 'Search Form', 
						'plugin_id' => 0,
						'function'=>'search',
						'params'=>'',
						'type' => 'sidebar',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),
					array('title' => 'Content', 
						'plugin_id' => 0,
						'function'=>'content',
						'params'=>'',
						'type' => 'content',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),
					array('title' => 'Side menu', 
						'plugin_id' => 0,
						'function'=>'sideMenu',
						'params'=>'',
						'type' => 'sidebar',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ))
						);
	}

}
