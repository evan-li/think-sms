<?php
return [
    // 驱动类型, 支持 zsd(众视达) aliyun(阿里云,待支持) ucpaas(云之讯,待支持)
    'driver' => 'zsd',

    // 众视达参数配置
    'zsd' => [
        // 行业短信参数配置
        'biz' => [
            'account' => 'account',
            'password' => 'password',
            'extno' => 'extno'
        ],
        // 营销短信参数配置
        'marketing' => [
            'account' => '',
            'password' => '',
            'extno' => ''
        ],
        // 信用卡营销短信参数配置
        'gray_marketing' => [
            'account' => '',
            'password' => '',
            'extno' => ''
        ],
    ]
];