<?php

return [
    'autoload' => false,
    'hooks' => [
        'action_begin' => [
            'geetest',
        ],
        'config_init' => [
            'geetest',
        ],
        'response_send' => [
            'loginvideo',
        ],
    ],
    'route' => [],
    'priority' => [],
];
