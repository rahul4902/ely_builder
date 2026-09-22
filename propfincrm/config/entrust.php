<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Entrust Models
    |--------------------------------------------------------------------------
    */
    'role' => 'App\Models\Role',
    'user' => 'App\Models\User',
    'user_model' => 'App\Models\User',
    'permission' => 'App\Models\Permissions',

    'models' => [
        'role' => 'App\Models\Role',
        'permission' => 'App\Models\Permissions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Entrust Tables
    |--------------------------------------------------------------------------
    */
    'roles_table' => 'roles',
    'permissions_table' => 'permissions',
    'permission_role_table' => 'permission_role',
    'role_user_table' => 'role_user',
    'user_table' => 'users',

    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'role_user' => 'role_user',
        'permission_role' => 'permission_role',
    ],

    /*
    |--------------------------------------------------------------------------
    | Foreign Keys
    |--------------------------------------------------------------------------
    */
    'user_foreign_key' => 'user_id',
    'role_foreign_key' => 'role_id',

    'foreign_keys' => [
        'user' => 'user_id',
        'role' => 'role_id',
        'permission' => 'permission_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => 'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware Configuration
    |--------------------------------------------------------------------------
    */
    'middleware' => [
        'register' => true,
        'handling' => 'abort',
        'handlers' => [
            'abort' => [
                'code' => 403,
                'message' => 'You don\'t Have a permission to Access this page.',
            ],
            'redirect' => [
                'url' => '/',
                'message' => [
                    'key' => 'error',
                    'content' => 'You don\'t Have a permission to Access this page',
                ],
            ],
        ],
    ],

];
