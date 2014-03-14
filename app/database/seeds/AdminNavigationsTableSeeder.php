<?php

class AdminNavigationsTableSeeder extends Seeder {

	public function run() {
		// Uncomment the below to wipe the table clean before populating
		// DB::table('settings')->truncate();

		DB::table('admin_navigations') -> insert(array( 
											array('plugin_id' => 1, 
												'icon' =>'icon-globe', 
												'background_color' =>'red',
												'order' => 1,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,),											
											array('plugin_id' => 3, 
												'icon' =>'icon-user', 
												'background_color' =>'yellow',
												'order' => 2,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
											array('plugin_id' => 4, 
												'icon' =>'icon-group', 
												'background_color' =>'green',
												'order' => 3,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
											array('plugin_id' => 5, 
												'icon' =>'icon-cloud', 
												'background_color' =>'blue',
												'order' => 4,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
											array('plugin_id' => 2, 
												'icon' =>'icon-cogs', 
												'background_color' =>'orange',
												'order' => 5,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
										)
									);

	}

}
