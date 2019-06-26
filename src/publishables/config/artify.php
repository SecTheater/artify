<?php

return [
    // Artify uses the namespace of models directory only if It's not working with ADR.
    'models' => [
        'namespace' => '\App\\',
    ],
    // set the name of the column in charge of the permissions within your Role Model.
    'permissions_column' => 'permissions',
    /*
     * Artify Uses cache in order to optimize your application performance, feel free to disable it or modify the duration.
     * Duration estimated in minutes.
     */
    'cache' => [
        'enabled' => true,
        'duration' => 10,
    ],
    'tenant' => null,
    'is_adr' => true,
    'is_multi_tenancy' => false,

];
