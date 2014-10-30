<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('users', function(Blueprint $table) {
			$table -> engine = 'InnoDB';
			$table -> increments('id');
			$table -> string('name');
			$table -> string('surname');
			$table -> string('email') -> unique();
			$table -> string('username') -> unique();
			$table -> string('avatar')-> nullable()->default(null);
			$table -> string('password');
			$table -> string('remember_token')-> nullable();			
			$table -> string('confirmation_code');
			$table -> boolean('confirmed') -> default(false);
			$table -> boolean('active') -> default(false);
			$table -> timestamp('last_login')->nullable();
			$table -> timestamps();
			$table -> softDeletes();
		});

		// Creates password reminders table
		Schema::create('password_reminders', function($table) {
			$table -> engine = 'InnoDB';
			$table -> string('email');
			$table -> string('token');
			$table -> boolean('used') -> default(false);
			$table -> timestamp('created_at');
			$table -> softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	   	Schema::drop('password_reminders');
		Schema::drop('users');
	}

}
