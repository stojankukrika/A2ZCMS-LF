<?php namespace App\Modules\Roles\Models;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class PermissionRole extends \Eloquent {
	protected $dates = ['deleted_at'];
	public $table = 'permission_role';
		 
	protected $guarded = array();

	public static $rules = array();

}
