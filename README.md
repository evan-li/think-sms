# Think-Sms
Sms Sender for ThinkPHP 5.1

# Installation
```
composer require evan-li/think-sms
```
> 安装后执行 evan-li/think-sms/db/init.sql, 初始化数据库结构

### 配置
`config/sms.php`:
```php
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
        // 灰度营销短信参数配置
        'gray_marketing' => [
            'account' => '',
            'password' => '',
            'extno' => ''
        ],
    ]
];
```

# 短信状态报告
使用众视达短信时, 可开启短信状态报告, 记录每条短信的接受状态. 
状态报告命令: `php think sms:zsd-report`,  在crontab中定时执行: `php think sms:zsd-report {count?}` 即可
> 其中`count`为可选参数, 默认值 1000

# Usage
```php
Sender::sendVerifyCodeSms('手机号', '短信内容', '短信签名');
```

