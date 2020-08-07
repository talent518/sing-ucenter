<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/6/27
 * Time: 17:00
 */

$categories=[
    'application',
    'curl',
    'MNSMessage',
    'WeiXinAds',
];

$config = [
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'app\core\CCFileTarget',
                    'categories' => $categories,
                    'maxFileSize' => 102400,
                ]
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.qq.com',
                'port' => '994',
                'username' => 'demo@qq.com',
                'password' => 'password',
                'encryption' => 'ssl',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['demo@qq.com' => 'yii2']
            ],
        ]
    ],
];

return $config;