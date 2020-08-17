<?php

return [
    'token_duration' => 86400,//token有效时长

    /*-----微信配置-----*/
    "MPHelper"=>[
        'wechat' => [
            '萝拉英语畅游' => [
                'app_id' => 'wx9965e0a0851c92af',
                'secret' => '983bb194e1695cf607370427cb6fdbf6',
                'mp_ads' => [
                    'user_action_set_id' => '1110558002'
                ],
            ],
            '唱唱启蒙小课堂' => [
                'app_id' => 'wx4161ef843c5c4759',
                'secret' => 'c87d2db0ebaa7008e6d699b150c7d3cb',
                'mp_ads' => [
                    'user_action_set_id' => '1110557958'
                ],
            ],
        ],
        'action_sets_url' => 'https://api.weixin.qq.com/marketing/user_action_sets/add?version=v1.0',       //创建 action_set_id
        'actions_url'     => 'https://api.weixin.qq.com/marketing/user_actions/add?version=v1.0',           //回传广告数据地址
    ],


    /*-----阿里云配置-----*/
    //MNS消息配置
    "MNSHelper"=>[
        'endpoint' => 'http://1818292842517217.mns.cn-shenzhen.aliyuncs.com',
        'accessKeyId' => 'LTAI4GGkK4bXL5jJvnpARYfQ',
        'accessKeySecret' => 'o8a0vCHgXyRQY9kAvA8x85Yq8SzHSG',
        'topicName' => 'system-event',
        'subscriptionName' => '市场系统事件通知',
    ],

	'sso' => [
        'host' => 'https://sso.changchangenglish.com',
        'app_id' => '8',
        'app_key' => 'OoUDxlPTSOAvqx4Hl93z9RON',
        'duration' => 300,
    ],
];
