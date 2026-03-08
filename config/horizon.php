<?php

use Laravel\Horizon\Horizon;

Horizon::night();

return [

    'domain' => env('HORIZON_DOMAIN'),
    'path'   => 'horizon',

    'use'    => 'default',

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    'middleware' => ['web'],

    'waits' => [
        'redis:default' => 60,
    ],

    'trim' => [
        'recent'           => 60,
        'pending'          => 60,
        'completed'        => 60,
        'recent_failed'    => 10080,
        'failed'           => 10080,
        'monitored'        => 10080,
    ],

    'silenced' => [],

    'metrics' => [
        'trim_snapshots' => [
            'job'   => 24,
            'queue' => 24,
        ],
    ],

    'fast_termination' => false,

    'memory_limit' => 64,

    'defaults' => [
        'supervisor-1' => [
            'connection'  => 'redis',
            'queue'       => ['default'],
            'balance'     => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses' => 1,
            'maxTime'     => 0,
            'maxJobs'     => 0,
            'memory'      => 128,
            'tries'       => 1,
            'timeout'     => 60,
            'nice'        => 0,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-products' => [
                'connection'   => 'redis',
                'queue'        => ['products'],
                'balance'      => 'simple',
                'processes'    => 2,
                'tries'        => 3,
                'timeout'      => 120,
            ],
            'supervisor-videos' => [
                'connection'   => 'redis',
                'queue'        => ['videos'],
                'balance'      => 'simple',
                'processes'    => 3,
                'tries'        => 2,
                'timeout'      => 300,
            ],
            'supervisor-ai' => [
                'connection'   => 'redis',
                'queue'        => ['ai'],
                'balance'      => 'simple',
                'processes'    => 2,
                'tries'        => 3,
                'timeout'      => 60,
            ],
            'supervisor-posting' => [
                'connection'   => 'redis',
                'queue'        => ['posting'],
                'balance'      => 'simple',
                'processes'    => 2,
                'tries'        => 2,
                'timeout'      => 600,
            ],
        ],

        'local' => [
            'supervisor-local' => [
                'connection'   => 'redis',
                'queue'        => ['default', 'products', 'videos', 'ai', 'posting'],
                'balance'      => 'simple',
                'processes'    => 3,
                'tries'        => 3,
                'timeout'      => 600,
            ],
        ],
    ],
];
