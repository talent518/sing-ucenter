<?php
/**
 * Created by PhpStorm.
 * User: zhangbing
 * Date: 2019/6/27
 * Time: 17:00
 */

$config = [
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'app\core\CCFileTarget',
                    'categories' => [
                        'application',
                        'curl',
                        'MNSMessage',
                        'WeiXinAds'
                    ],
                    'logVars' => [],
                    'maxFileSize' => 102400,
                    'logFile' => '/var/log/yii2/ucenter-service/app-' . date("Y-m-d") . '.log',
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
        ],
        'msgNotice' => [
            'class' => 'app\core\MsgNotice',
            'ssoAppId' => 18,
            'mnsEndPoint' => 'http://1818292842517217.mns.cn-shenzhen-internal.aliyuncs.com/',
            'mnsAccessId' => 'LTAI4GGkK4bXL5jJvnpARYfQ',
            'mnsAccessKey' => 'o8a0vCHgXyRQY9kAvA8x85Yq8SzHSG',
            'mnsTopic' => 'system-event-test',
        ]
    ],
];

return $config;