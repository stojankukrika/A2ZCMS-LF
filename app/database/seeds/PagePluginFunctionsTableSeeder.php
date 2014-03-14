<?php

class PagePluginFunctionsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('page_plugin_functions')->truncate();
		
		$page_id = Page::first()->id;

		DB::table('page_plugin_functions')->insert(array( 
					array('page_id' => $page_id, 
						'plugin_function_id' => PluginFunction::find(1)->id,
						'order' => '1',
						'param' => '',
						'type' => '',
						'value' => '',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ),
					array('page_id' => $page_id, 
						'plugin_function_id' => PluginFunction::find(3)->id,
						'order' => '1',
						'param' => '',
						'type' => '',
						'value' => '',
						'created_at' => new DateTime, 
						'updated_at' => new DateTime, ))
				);
	}

}
