<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePluginsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('plugins', function(Blueprint $table) {
			$table -> increments('id');
			$table -> string('name');
			$table -> string('title');
			$table -> string('function_id')->nullable();
			$table -> string('function_grid')->nullable();
			$table -> tinyint('can_uninstall',1)->default('1');
			$table -> string('pluginversion',5)->nullable();
			$table -> tinyint('active',1)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('plugins');
	}

}
