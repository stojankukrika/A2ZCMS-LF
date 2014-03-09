<?php namespace App\Modules\Users\Models;

class AssignedRoles extends \Eloquent {
	protected $table = "assigned_roles";
	protected $softDelete = true;
	
	protected $guarded = array();
	public static $rules = array();

}
