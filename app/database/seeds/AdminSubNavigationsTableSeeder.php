<?php

class AdminSubNavigationsTableSeeder extends Seeder {

	public function run() {
		// Uncomment the below to wipe the table clean before populating
		// DB::table('settings')->truncate();

		DB::table('admin_subnavigations') -> insert(array( 
											array('admin_navigation_id' => 1, 
												'title' => 'Navigation group', 
												'url' => 'pages/navigationgroups', 
												'icon' =>'icon-th-list',
												'order' => 1,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,),											
											array('admin_navigation_id' => 1, 
												'title' => 'Pages', 
												'url' => 'pages', 
												'icon' =>'icon-th-large',
												'order' => 2,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
											array('admin_navigation_id' => 1, 
												'title' => 'Navigation', 
												'url' => 'pages/navigation', 
												'icon' =>'icon-th',
												'order' => 3,
												'created_at' => new DateTime, 
												'updated_at' => new DateTime,), 
										)
									);

	}

}
