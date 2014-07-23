<?php namespace App\Modules\Blogs\Models;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

use String;
class BlogComment extends \Eloquent {

	protected $dates = ['deleted_at'];
	protected $table = "blog_comments";

	/*
	 *//**
	 * Get the comment's content.
	 *
	 * @return string
	 */
	public function content() {
		return nl2br($this -> content);
	}

	/**
	 * Get the comment's author.
	 *
	 * @return User
	 */
	public function author() {
		return $this -> belongsTo('User', 'user_id');
	}

	/**
	 * Get the comment's post's.
	 *
	 * @return Blog\Comment
	 */
	public function comment() {
		return $this -> belongsTo('Blog');
	}

	/**
	 * Get the post's author.
	 *
	 * @return User
	 */
	public function user() {
		return $this -> belongsTo('User', 'user_id');
	}

	/**
	 * Get the date the post was created.
	 *
	 * @param \Carbon|null $date
	 * @return string
	 */
	public function date($date = null) {
		if (is_null($date)) {
			$date = $this -> created_at;
		}

		return String::date($date);
	}

	/**
	 * Returns the date of the blog post creation,
	 * on a good and more readable format :)
	 *
	 * @return string
	 */
	public function created_at() {
		return $this -> date($this -> created_at);
	}

	/**
	 * Returns the date of the blog post last update,
	 * on a good and more readable format :)
	 *
	 * @return string
	 */
	public function updated_at() {
		return $this -> date($this -> updated_at);
	}

}
