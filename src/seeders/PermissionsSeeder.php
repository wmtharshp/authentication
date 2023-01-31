<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insert_data = [
                [
                    'title' => 'users',
                    'name' => [
                        'users.index',
                        'users.show',
                        'users.create',
                        'users.store',
                        'users.edit',
                        'users.update',
                        'users.destory',
                    ],
                    'description' => [
                        'List of users',
                        'Show user details',
                        'User create from',
                        'Store user details',
                        'Update user from',
                        'Update user details',
                        'Delete user'
                    ]
                ],
                [

                    'title' => 'roles',
                    'name' => [
                        'roles.index',
                        'roles.show',
                        'roles.create',
                        'roles.store',
                        'roles.edit',
                        'roles.update',
                        'roles.destory',
                    ],
                    'description' => [
                        'List of roles',
                        'Show role details',
                        'Role create from',
                        'Store role details',
                        'Update role from',
                        'Update role details',
                        'Delete role'
                    ]
                ],
                [

                        'title' => 'permissions',
                        'name' => [
                            'permissions.index',
                            'permissions.show',
                            'permissions.create',
                            'permissions.store',
                            'permissions.edit',
                            'permissions.update',
                            'permissions.destory',
                        ],
                        'description' => [
                            'List of permissions',
                            'Show permission details',
                            'Permission create from',
                            'Store permission details',
                            'Update permission from',
                            'Update permission details',
                            'Delete permission'
                        ]
                        ],
                        [

                            'title' => 'Dashboard',
                            'name' => [
                               'home'
                            ],
                            'description' => [
                                'Home Page'
                            ]
                    ]
            ];
            foreach($insert_data as $k => $value){
                $insert_data_new = array();
                $insert_data_new['title'] = $value['title'];
                foreach($value['name'] as $key => $val){
                    $insert_data_new['name'] = $val;
                    $insert_data_new['description'] = $value['description'][$key];
                    Permission::create($insert_data_new);
                }
            }
    }
}
