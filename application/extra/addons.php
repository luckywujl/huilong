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
        'sms_send' => [
            'smsbao',
        ],
        'sms_notice' => [
            'smsbao',
        ],
        'sms_check' => [
            'smsbao',
        ],
    ],
    'route' => [],
    'priority' => [],
];
