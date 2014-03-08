<?php namespace App\Modules\Adminmenu\Models;

class Adminsubmenu extends \Eloquent {

	protected $table = "admin_subnavigations";
	protected $softDelete = true;

	public function getPresenter() {
		return new CommentPresenter($this);
	}

}
