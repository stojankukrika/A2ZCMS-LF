<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('settings', function(Blueprint $table) {
			$table -> increments('id');
			$table -> string('varname');
			$table -> string('vartitle')
			$table -> string('groupname');
			$table -> text('value')->nullable();
			$table -> text('defaultvalue')->nullable();
			$table -> string('type',50)->nullable();
			$table -> string('rule',50)->nullable();
			$table -> string('rule');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('settings');
	}

}
