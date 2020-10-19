<?php
return [
    'components' => [
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'autodetectCluster' => false,
            'nodes' => [
                ['http_address' => '172.18.124.62:9200'],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            // 配置主服务器
            'dsn' => "mysql:host=127.0.0.1;dbname=sing-ucenter;port=3306",
            'username' => 'root',
            'password' => 'Sing123@db.com',
            'charset' => 'utf8mb4',
            'enableSlaves' => true,
            'enableSchemaCache' => true,
            'schemaCache' => 'cache', // Name of the cache component used to store schema information
            'schemaCacheDuration' => 600,
            // 配置从服务器
            'slaveConfig' => [
                'username' => 'root',
                'password' => 'Sing123@db.com',
                'charset' => 'utf8mb4',
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ],
            ],

            // 配置从服务器组
            'slaves' => [
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-ucenter;port=3306"],
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-ucenter;port=3306"],
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-ucenter;port=3306"],
            ],
        ],
        'db_user' => [
            'class' => 'yii\db\Connection',
            // 配置主服务器
            'dsn' => "mysql:host=127.0.0.1;dbname=sing-user;port=3306",
            'username' => 'root',
            'password' => 'Sing123@db.com',
            'charset' => 'utf8mb4',
            'enableSlaves' => true,
            'enableSchemaCache' => true,
            'schemaCache' => 'cache', // Name of the cache component used to store schema information
            'schemaCacheDuration' => 600,
            // 配置从服务器
            'slaveConfig' => [
                'username' => 'root',
                'password' => 'Sing123@db.com',
                'charset' => 'utf8mb4',
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ],
            ],

            // 配置从服务器组
            'slaves' => [
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-user;port=3306"],
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-user;port=3306"],
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-user;port=3306"],
            ],
        ],
        'db_order' => [
            'class' => 'yii\db\Connection',
            // 配置主服务器
            'dsn' => "mysql:host=127.0.0.1;dbname=sing-order;port=3306",
            'username' => 'root',
            'password' => 'Sing123@db.com',
            'charset' => 'utf8mb4',
            'enableSlaves' => true,
            'enableSchemaCache' => true,
            'schemaCache' => 'cache', // Name of the cache component used to store schema information
            'schemaCacheDuration' => 600,
            // 配置从服务器
            'slaveConfig' => [
                'username' => 'root',
                'password' => 'Sing123@db.com',
                'charset' => 'utf8mb4',
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ],
            ],

            // 配置从服务器组
            'slaves' => [
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-order;port=3306"],
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-order;port=3306"],
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-order;port=3306"],
            ],
        ],
        'db_pub' => [
            'class' => 'yii\db\Connection',
            // 配置主服务器
            'dsn' => "mysql:host=127.0.0.1;dbname=sing-pub;port=3306",
            'username' => 'root',
            'password' => 'Sing123@db.com',
            'charset' => 'utf8mb4',
            'enableSlaves' => true,
            'enableSchemaCache' => true,
            'schemaCache' => 'cache', // Name of the cache component used to store schema information
            'schemaCacheDuration' => 600,
            // 配置从服务器
            'slaveConfig' => [
                'username' => 'root',
                'password' => 'Sing123@db.com',
                'charset' => 'utf8mb4',
                'attributes' => [
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ],
            ],

            // 配置从服务器组
            'slaves' => [
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-pub;port=3306"],
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-pub;port=3306"],
                ['dsn' => "mysql:host=127.0.0.1;dbname=sing-pub;port=3306"],
            ],
        ],
        //redis链接配置
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => '6379',
            'password' => 'Sing%41@',
            'database' => 15,
        ],
    ]
];