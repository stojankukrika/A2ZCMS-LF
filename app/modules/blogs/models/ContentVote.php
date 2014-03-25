<?php namespace App\Modules\Blogs\Models;

class ContentVote extends \Eloquent  {

	public $table = 'content_votes';
	protected $softDelete = true;
	protected $guarded = array();	
	/**
	 * Get the date the blog was created.
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
	
}
