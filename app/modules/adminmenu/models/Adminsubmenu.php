<?php namespace App\Modules\Adminmenu\Models;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Adminsubmenu extends \Eloquent {

	protected $table = "admin_subnavigations";
	protected $dates = ['deleted_at'];

	public function getPresenter() {
		return new CommentPresenter($this);
	}

}
