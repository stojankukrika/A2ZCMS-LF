<?php namespace App\Modules\Plugins\Seeds;

use Eloquent, Str;

class DatabaseSeeder extends \Seeder {

	/**
	 * Run the database seeds.
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
	}

}
