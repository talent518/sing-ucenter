<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/../environments/' . YII_ENV . '/config/params-local.php')
);

$config = [
    'id' => 'yii2-api',
    'language' => 'zh-CN',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue1', 'queue2', 'queue3', 'queue4'],
    'timeZone' => 'Asia/Shanghai',
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'request' => [
            'class' => 'app\core\Request',
            'parsers' => [
                'application/json' => [
                    'class' => 'yii\web\JsonParser',
                    'asArray' => true
                ],
                'text/json' => [
                    'class' => 'yii\web\JsonParser',
                    'asArray' => true
                ],
            ],
        ],
        'cache' => [
            'class' => 'app\core\RedisCache',
        ],
        'queue1' => [
            'class' => 'yii\queue\redis\Queue',
            'redis' => 'redis',
            'channel' => 'queue1',
            'attempts' => 3,
        ],
        'queue2' => [
            'class' => 'yii\queue\redis\Queue',
            'redis' => 'redis',
            'channel' => 'queue2',
            'attempts' => 3,
        ],
        'queue3' => [
            'class' => 'yii\queue\redis\Queue',
            'redis' => 'redis',
            'channel' => 'queue3',
            'attempts' => 3,
        ],
        'queue4' => [
            'class' => 'yii\queue\redis\Queue',
            'redis' => 'redis',
            'channel' => 'queue4',
            'attempts' => 3,
        ],
        'user' => [
            'identityClass' => 'app\models\account\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'cache' => YII_ENV_PROD ? 'cache' : false,
            'rules' => [
                '' => '/site/index',
                [
                    'class' => 'app\core\UrlRule',
                ],
                '/<module:\w+>/<controller:\w+>/<action:\w+>' => '/<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'enum' => 'enum.php',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'class' => 'app\core\ErrorHandler'
        ],
        'mutex' => [
            'class' => 'yii\redis\Mutex',
            'redis' => 'redis',
            'expire' => 120,
        ],
    ],
    'modules' => [
        'open' => [
            'class' => 'app\modules\open\Module',
            'businessNamespace' => 'app\modules\open\businesses',
            'controllerNamespace' => 'app\modules\open\controllers',
            'autoRegisterRouters' => true
        ],
        'watch' => [
            'class' => 'app\modules\watch\Module',
            'businessNamespace' => 'app\modules\watch\businesses',
        	'controllerNamespace' => 'app\modules\watch\controllers',
            'autoRegisterRouters' => true
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '172.18.*.*', '172.19.*.*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '172.18.*.*', '172.19.*.*'],
    ];
}

return $config;
