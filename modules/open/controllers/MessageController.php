<?php

namespace app\modules\open\controllers;

use app\core\CCController;
use app\core\MNSHelper;

class MessageController extends CCController
{
    /**
     * 链接 $url/open/message/receive
     * MNS消息处理。
     * @package app\modules\open\controllers
     */
    public function actionReceive()
    {
        try{
            $topic = $xml = MNSHelper::instance()->topicGetCallbackMsg(false);
            //订阅名称
            $SubscriptionName=MNSHelper::instance()->subscriptionName;
            if(!isset($topic['SubscriptionName']) || $topic['SubscriptionName'] != $SubscriptionName){
                \Yii::warning("MNS消息-订阅名称错误，不是本系统处理可以处理的", 'MNSMessage');
                \Yii::$app->response->statusCode = 204;
                return 'success';
            }
            //主题名称
            $topic_name=MNSHelper::instance()->topicName;
            if(!isset($topic['TopicName']) || $topic['TopicName'] != $topic_name){
                \Yii::warning("MNS消息-主题名称错误，不是本系统处理可以处理的", 'MNSMessage');
                \Yii::$app->response->statusCode = 204;
                return 'success';
            }
            //消息的数据
            $data = json_decode(base64_decode($topic['Message']), true);
            if(empty($data)){
                \Yii::warning("MNS消息-数据格式不正确或数据为空，无法处理", 'MNSMessage');
                \Yii::$app->response->statusCode = 204;
                return 'success';
            }

            \Yii::info($data, 'MNSMessage');

            //过滤掉没有category的消息
            if(!isset($data['event_type'])){
                \Yii::warning("MNS消息-事件不存在。无法处理", 'MNSMessage');
                \Yii::$app->response->statusCode = 204;
                return 'success';
            }
            $event_type=$data['event_type'];
            //开始处理
            $event_type_class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $event_type)));
            $className = 'app\services\MNSMessageService'.'\\'.$event_type_class.'Service';
            $obj = new $className();
            $res=$obj->run($event_type, $data);

            \Yii::info($res, 'MNSMessage');
            \Yii::$app->response->statusCode = 204;
            return 'success';

        } catch (\Error $e){
            \Yii::error($e, 'MNSMessage');
            \Yii::$app->response->statusCode = 500;
            return $e->getMessage();
        } catch (\Exception $e){
            \Yii::error($e, 'MNSMessage');
            \Yii::$app->response->statusCode = 500;
            return $e->getMessage();
        }
    }

}