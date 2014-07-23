<?php namespace App\Modules\Users\Models;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class AssignedRoles extends \Eloquent {
	protected $dates = ['deleted_at'];
	protected $table = "assigned_roles";
	protected $softDelete = true;
	
	protected $guarded = array();
	public static $rules = array();

}
