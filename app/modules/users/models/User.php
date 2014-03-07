<?php namespace App\Modules\Users\Models;

class User extends \Eloquent {

	protected $guarded = array('id');

	/**
	 * Find page
	 * @param  Query  $query
	 * @return Query
	 */
	public static function scopePage($query, $slug = null)
	{
		if ($slug) $query->where('slug', $slug);

		return $query->where('type', 'page');
	}

}
