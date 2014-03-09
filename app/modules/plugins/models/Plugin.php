<?php namespace App\Modules\Plugins\Models;

class Plugin extends \Eloquent {

	protected $table = "plugins";
	protected $softDelete = true;

	/**
	 * Returns a formatted varname entry,
	 * this ensures that line breaks are returned.
	 *
	 * @return string
	 */
	public function name() {
		return nl2br($this -> name);
	}

	/**
	 * Returns a formatted groupname entry,
	 * this ensures that line breaks are returned.
	 *
	 * @return string
	 */
	public function title() {
		return nl2br($this -> title);
	}

	/**
	 * Returns a formatted value entry,
	 * this ensures that line breaks are returned.
	 *
	 * @return string
	 */
	public function function_id() {
		return nl2br($this -> function_id);
	}

	/**
	 * Returns a formatted defaultvalue entry,
	 * this ensures that line breaks are returned.
	 *
	 * @return string
	 */
	public function function_grid() {
		return nl2br($this -> function_grid);
	}

	public function getPresenter() {
		return new CommentPresenter($this);
	}

}