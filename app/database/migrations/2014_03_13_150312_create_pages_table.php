<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('pages', function(Blueprint $table) {
			$table -> increments('id');
			$table -> string('title');
			$table -> string('slug');
			$table -> text('content');
			$table -> string('image')->nullable();
			$table -> boolean('status');
			$table -> string('meta_title')-> nullable();
			$table -> string('meta_description')-> nullable();
			$table -> string('meta_keywords')-> nullable();
			$table -> text('page_css')-> nullable();
			$table -> text('page_javascript')-> nullable();
			$table -> boolean('sidebar');
			$table -> boolean('showtags');
			$table -> boolean('showtitle');
			$table -> boolean('showvote');
			$table -> boolean('showdate');
			$table -> integer('voteup');
			$table -> integer('votedown');
			$table -> string('password');
			$table -> string('tags')-> nullable();
			$table -> integer('hits');			
			$table -> timestamps();
			$table -> softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('pages');
	}

}
