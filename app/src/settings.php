<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => realpath(__DIR__ . '/../../templates') . '/',
        ],

        // log settings
        'logger' => [
            'name' => 'freedict-search-app',
            'path' => __DIR__ . '/../../logs/app.log',
            'level' => 'debug',
        ],
        'db' =>[
            'driver' => 'dsn',
            'dsn' => 'sqlite:' . dirname(__DIR__) . '/data/freedict-search.sqlite'
        ],
        'freedict' =>[
            'rootdir' => dirname(dirname(__DIR__)) . '/../fd-dictionaries',
            'dictionaries' => [
                'deu-eng'
            ]
        ]
    ],
];
