<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages', function(Blueprint $table) {
			$table->increments('id');
			$table -> integer('user_id_from') -> unsigned();
			$table -> foreign('user_id_from') -> references('id') -> on('users') -> onDelete('cascade');
			$table -> integer('user_id_to') -> unsigned();
			$table -> foreign('user_id_to') -> references('id') -> on('users') -> onDelete('cascade');
			$table -> string('subject');
			$table -> text('content');
			$table -> boolean('read');
			$table -> timestamp('deleted_at_receiver') -> nullable();
			$table -> timestamp('deleted_at_sender') -> nullable();			
			$table -> timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
		Schema::drop('messages');
	}

}
