<?php namespace App\Modules\Adminmenu\Models;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Adminmenu extends \Eloquent {
	 
	protected $table = "admin_navigations";
	protected $dates = ['deleted_at'];

	public function getPresenter() {
		return new CommentPresenter($this);
	}

}
