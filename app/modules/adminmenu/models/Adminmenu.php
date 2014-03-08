<?php namespace App\Modules\Adminmenu\Models;

class Adminmenu extends \Eloquent {

	protected $table = "admin_navigations";
	protected $softDelete = true;

	public function getPresenter() {
		return new CommentPresenter($this);
	}

}
