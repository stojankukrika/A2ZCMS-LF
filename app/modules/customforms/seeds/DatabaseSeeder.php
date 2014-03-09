<?php

class DatabaseSeeder extends Seeder {

	public function run() {
		Eloquent::unguard();

		// Add calls to Seeders here
		$this -> call('CustomFormsTableSeeder');
		$this -> call('CustomFormFieldsTableSeeder');
		$this -> call('CustomFormPermissionsTableSeeder');
	}

}
