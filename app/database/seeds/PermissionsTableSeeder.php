<?php

class PermissionsTableSeeder extends Seeder {

	public function run() {
		
		$permissions = array( 
						array('name' => 'manage_blogs', 'display_name' => 'Manage blogs',
							'is_admin' => 1, 'created_at' => new DateTime), 
						array('name' => 'manage_blog_categris', 'display_name' => 'Manage blog categris',
							'is_admin' => 1, 'created_at' => new DateTime), 
						array('name' => 'manage_comments', 'display_name' => 'Manage comments',
							'is_admin' => 1, 'created_at' => new DateTime), 
						array('name' => 'manage_users', 'display_name' => 'Manage users',
							'is_admin' => 1, 'created_at' => new DateTime), 
						array('name' => 'manage_roles', 'display_name' => 'Manage roles',
							'is_admin' => 1, 'created_at' => new DateTime), 
						array('name' => 'post_blog_comment','display_name' => 'Post blog comment',
							'is_admin' => 0, 'created_at' => new DateTime), 
						array('name' => 'manage_navigation', 'display_name' => 'Manage navigation',
							'is_admin' => 1, 'created_at' => new DateTime), 
						array('name' => 'manage_pages', 'display_name' => 'Manage pages',
							'is_admin' => 1, 'created_at' => new DateTime), 
						array('name' => 'manage_navigation_groups', 'display_name' => 'Manage navigation groups',
							'is_admin' => 1, 'created_at' => new DateTime),
						array('name' => 'manage_settings', 'display_name' => 'Manage settings',
							'is_admin' => 1, 'created_at' => new DateTime),
				
					);

		DB::table('permissions') -> insert($permissions);

		//DB::table('permission_role')->delete();

		$permissions = array( array('role_id' => 1, 'permission_id' => 1,'created_at' => new DateTime, 
						'updated_at' => new DateTime,), 
								array('role_id' => 1, 'permission_id' => 2,'created_at' => new DateTime, 
						'updated_at' => new DateTime,), 
								array('role_id' => 1, 'permission_id' => 3,'created_at' => new DateTime, 
						'updated_at' => new DateTime,), 
								array('role_id' => 1, 'permission_id' => 4,'created_at' => new DateTime, 
						'updated_at' => new DateTime,), 
								array('role_id' => 1, 'permission_id' => 5,'created_at' => new DateTime, 
						'updated_at' => new DateTime,), 
								array('role_id' => 1, 'permission_id' => 6,'created_at' => new DateTime, 
						'updated_at' => new DateTime,), 
								array('role_id' => 1, 'permission_id' => 7,'created_at' => new DateTime, 
						'updated_at' => new DateTime,), 
								array('role_id' => 1, 'permission_id' => 8,'created_at' => new DateTime, 
						'updated_at' => new DateTime,), 
								array('role_id' => 1, 'permission_id' => 9,'created_at' => new DateTime, 
						'updated_at' => new DateTime,),
								array('role_id' => 1, 'permission_id' => 15,'created_at' => new DateTime, 
						'updated_at' => new DateTime,),);

		DB::table('permission_role') -> insert($permissions);
	}

}
