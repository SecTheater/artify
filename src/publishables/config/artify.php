<?php

return [
    /**
     * Artify uses the User Model to do things related to users using The Artifies, Such as assigning a role to a specific user and so on, Feel free to change the names.
     */
    'models' => [
        'namespace' => '\App\\',
        'user' => 'User',
        'role' => 'Role'
    ],
    // set the name of the column in charge of the permissions within your Role Model.
    'permissions_column' => 'permissions',
    /**
     * Artify Uses cache in order to optimize your application performance, feel free to disable it or modify the duration.
     * Duration estimated in minutes.
     */
    'cache' => [
        'enabled' => true,
        'duration' => 10
    ]
];