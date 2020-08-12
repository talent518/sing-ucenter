<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/../environments/' . YII_ENV . '/config/params-local.php')
);

$config = [
    'id' => 'ucenter-console',
    'timeZone' => 'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue1', 'queue2', 'queue3', 'queue4'],
    'controllerNamespace' => 'app\commands\controllers',
    'components' => [
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
        'mutex' => [
            'class' => 'yii\redis\Mutex',
            'redis' => 'redis',
            'expire' => 3600,
        ],
    ],
    'params' => $params,
];

return $config;
