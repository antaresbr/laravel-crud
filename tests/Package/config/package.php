<?php

return [

    'route' => [
        'prefix' => [
            'web' => env('PACKAGE_ROUTE_PREFIX_WEB', 'tests/package'),
            'api' => env('PACKAGE_ROUTE_PREFIX_API', 'api/tests/package'),
        ],
    ],

];
