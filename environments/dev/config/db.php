<?php
$DB_WRITE_HOST='172.18.1.18';
$DB_WRITE_PORT='3306';
$DB_READ_HOST='172.18.1.18';
$DB_READ_PORT='3306';
$DB_USER='root';
$DB_PASSWORD='Sing123@db.com';

return [
    'components' => [
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'autodetectCluster' => false,
            'nodes' => [
                ['http_address' => '172.18.1.18:9200'],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            // 配置主服务器
            'dsn' => "mysql:host={$DB_WRITE_HOST};dbname=sing-ucenter;port={$DB_WRITE_PORT}",
            'username' => $DB_USER,
            'password' => $DB_PASSWORD,
            'charset' => 'utf8mb4',
            'enableSlaves' => true,
            'enableSchemaCache' => true,
            'schemaCache' => 'cache', // Name of the cache component used to store schema information
            'schemaCacheDuration' => 600,
            // 配置从服务器
            'slaveConfig' => [
                'username' => $DB_USER,
                'password' => $DB_PASSWORD,
                'charset' => 'utf8mb4',
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ],
                'enableSlaves' => true,
                'enableSchemaCache' => true,
                'schemaCache' => 'cache', // Name of the cache component used to store schema information
                'schemaCacheDuration' => 600,
            ],

            // 配置从服务器组
            'slaves' => [
                ['dsn' => "mysql:host={$DB_READ_HOST};dbname=sing-ucenter;port={$DB_READ_PORT}"],
                ['dsn' => "mysql:host={$DB_READ_HOST};dbname=sing-ucenter;port={$DB_READ_PORT}"],
                ['dsn' => "mysql:host={$DB_READ_HOST};dbname=sing-ucenter;port={$DB_READ_PORT}"],
            ],
        ],
        //redis链接配置
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => '6379',
            'database' => 2,
        ],
    ]
];
