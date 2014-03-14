<?php

class PermissionsTableSeeder extends Seeder {

	public function run() {

		DB::table('permissions') -> insert(array( 
						array('name' => 'manage_users', 'display_name' => 'Manage users',
							'is_admin' => 1), 
						array('name' => 'manage_roles', 'display_name' => 'Manage roles',
							'is_admin' => 1),
						array('name' => 'manage_navigation', 'display_name' => 'Manage navigation',
							'is_admin' => 1),
						array('name' => 'manage_navigation_groups', 'display_name' => 'Manage navigation groups',
							'is_admin' => 1),
						array('name' => 'manage_settings', 'display_name' => 'Manage settings',
							'is_admin' => 1),
						array('name' => 'manage_plugins', 'display_name' => 'Manage plugins',
							'is_admin' => 1)));
				
		DB::table('permission_role') -> insert(array( array('role_id' => 1, 'permission_id' => 1), 
								array('role_id' => 1, 'permission_id' => 2), 
								array('role_id' => 1, 'permission_id' => 3), 
								array('role_id' => 1, 'permission_id' => 4), 
								array('role_id' => 1, 'permission_id' => 5), 
								array('role_id' => 1, 'permission_id' => 6)));
	}

}
