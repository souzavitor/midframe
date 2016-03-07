<?php
return [
    'routes' => [
        [
            'name' => 'home',
            'path' => '/{message}',
            'middleware' => App\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
    ],
];
