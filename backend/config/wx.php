<?php

return [
    'mp' => [
        'app_id' => '',
        'secret' => '',
        
        'log' => [
            'level' => 'debug',
            'file' => app()->getRuntimePath().'/wechat/mp/log/'.date('Ym').'/'.date('d').'.log',
        ],
    ],
    'gh' => [
        'app_id' => '',
        'secret' => '',
        'log' => [
            'level' => 'debug',
            'file' => app()->getRuntimePath().'/wechat/gh/log/'.date('Ym').'/'.date('d').'.log',
        ],
    ],
    'pay' => [
        // 必要配置
        'app_id'             => '',
        'mch_id'             => '',
        'key'                => '',   // API 密钥
    
        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path'          => '', // XXX: 绝对路径！！！！
        'key_path'           => '',      // XXX: 绝对路径！！！！
        'log' => [
            'level' => 'debug',
            'file' => app()->getRuntimePath().'/wechat/pay/log/'.date('Ym').'/'.date('d').'.log',
        ],
    ]
];