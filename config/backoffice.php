<?php

return [
    'context' => [
        'backoffice' => [
            'default_context' => true,
            'global_url_prefix' => 'backoffice',
            'favicon_url' => null,
            //'menu_factory' => Digbang\Backoffice\Support\MenuFactory::class,
            'auth' => [
                'login-route' => 'backoffice.auth.login',
                'contact' => 'backoffice@digbang.com',
                'global_table_prefix' => 'backoffice_',
                'users' => [
                    'url' => 'backoffice-users',
                    'custom_table' => null,
                    'custom_mapping' => null,
                    'custom_repository' => Digbang\Backoffice\Repositories\DoctrineUserRepository::class,
                ],
                'roles' => [
                    'enabled' => true,
                    'url' => 'backoffice-roles',
                    'custom_table' => null,
                    'custom_join_table' => null,
                    'custom_mapping' => null,
                    'custom_repository' => Digbang\Backoffice\Repositories\DoctrineRoleRepository::class,
                ],
                'permissions' => [
                    'enabled' => true,
                    'repository' => \Digbang\Security\Permissions\RoutePermissionRepository::class,
                    'custom_user_permission_mapping' => null,
                    'custom_user_permission_table' => null,
                    'custom_role_permission_mapping' => null,
                    'custom_role_permission_table' => null,
                ],
                'activations' => [
                    'custom_table' => null,
                    'expiration' => null,
                    'lottery' => null,
                    'custom_mapping' => null,
                    'custom_repository' => null,
                ],
                'reminders' => [
                    'custom_table' => null,
                    'expiration' => null,
                    'lottery' => null,
                    'custom_mapping' => null,
                    'custom_repository' => null,
                ],
                'persistences' => [
                    'single' => false,
                    'custom_table' => null,
                    'custom_mapping' => null,
                    'custom_repository' => null,
                ],
                'throttles' => [
                    'enabled' => true,
                    'global' => [
                        'interval' => null,
                        'thresholds' => null,
                    ],
                    'ip' => [
                        'interval' => null,
                        'thresholds' => null,
                    ],
                    'user' => [
                        'interval' => null,
                        'thresholds' => null,
                    ],
                    'custom_table' => null,
                    'custom_repository' => null,
                    'custom_mappings' => [
                        'custom_throttle_mapping' => null,
                        'custom_ip_throttle_mapping' => null,
                        'custom_global_throttle_mapping' => null,
                        'custom_user_throttle_mapping' => null,
                    ],
                ],
            ],
            'gen' => [
                'controllers_dir' => base_path('app/Http/Controllers/Backoffice'),
                'controllers_namespace' => 'App\Http\Controllers\Backoffice',
                'apis_path' => base_path('src/Apis'),
            ],
            'emails' => [
                'address' => 'backoffice@digbang.com',
                'name' => 'Backoffice',
            ],
            'menu' => [
                'Backoffice' => [
                    'Backoffice users' => [
                        'icon' => 'user',
                        'action' => App\Http\Controllers\Backoffice\UserController::class . '@index',
                    ],
                    'Backoffice groups' => [
                        'icon' => 'group',
                        'action' => App\Http\Controllers\Backoffice\GroupController::class . '@index',
                    ],
                ],
            ],
        ],
    ],
];
