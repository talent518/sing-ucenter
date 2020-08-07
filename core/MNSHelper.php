<?php
namespace app\core;

use AliyunMNS\Client;
use AliyunMNS\Exception\MessageNotExistException;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Exception\QueueNotExistException;
use AliyunMNS\Model\SendMessageRequestItem;
use AliyunMNS\Model\SubscriptionAttributes;
use AliyunMNS\Requests\BatchReceiveMessageRequest;
use AliyunMNS\Requests\BatchSendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Requests\CreateTopicRequest;
use AliyunMNS\Requests\PublishMessageRequest;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\ListTopicRequest;
use AliyunMNS\Responses\SendMessageResponse;


/**
 * Aliyun 消息服务帮助类
 * 相关文档可参考以下链接
 *  https://help.aliyun.com/product/27412.html
 * @author liujing(lewkinglove@gmail.com)
 */
class MNSHelper
{
    /**
     *
     * @var string
     */
    private $endpoint;

    /**
     *
     * @var string
     */
    private $accessKeyId;

    /**
     *
     * @var string
     */
    private $accessKeySecret;

    /**
     *
     * @var string
     */
    public $topicName;

    /**
     *
     * @var string
     */
    public $subscriptionName;


    private $client;

    /**
     * 日志
     *
     * @var []
     */
    public $logs = [];

    public function setEndpoint($value)
    {
        $this->endpoint = $value;
    }

    public function setAccessKeyId($value)
    {
        $this->accessKeyId = $value;
    }

    public function setAccessKeySecret($value)
    {
        $this->accessKeySecret = $value;
    }

    public function settopicName($value)
    {
        $this->topicName = $value;
    }

    public function setsubscriptionName($value)
    {
        $this->subscriptionName = $value;
    }


    /**
     * 获取client
     * @param bool $reset 是否重置
     * @return Client
     */
    protected function getClient($reset = false)
    {
        if (! $this->client || $reset) {
            $this->client = new Client($this->endpoint, $this->accessKeyId, $this->accessKeySecret);
        }
        return $this->client;
    }

    /**
     * 创建主题
     *
     * @param string $name 主题名称
     * @throws MnsException
     * @return \AliyunMNS\Responses\CreateTopicResponse
     */
    protected function topicCreate($name)
    {
        $request = new CreateTopicRequest($name);
        return $this->getClient()->createTopic($request);
    }

    /**
     * 删除主题
     *
     * @param string $name  主题的名称
     * @throws MnsException
     * @return \AliyunMNS\Responses\DeleteTopicResponse
     */
    protected function topicDelete($name)
    {
        return $this->getClient()->deleteTopic($name);
    }

    /**
     * 主题列表
     * @return \AliyunMNS\Responses\ListTopicResponse
     */
    public function Topiclist()
    {
        $request = new ListTopicRequest();
        return $this->getClient()->listTopic($request);
    }

    /**
     * 获取主题对象
     *
     * @param string $name
     *            获取主题
     * @throws MnsException
     * @return \AliyunMNS\Topic
     */
    protected function topicObjectGet($name)
    {
        return $this->getClient()->getTopicRef($name);
    }

    /**
     * 订阅主题
     *
     * @param string|\AliyunMNS\Topic $topic
     *            主题的名称或主题类
     * @param string $subscriptionName
     *            订阅识别名称
     * @param string $subscriptionUrl
     *            订阅事件的回调地址
     * @throws MnsException
     * @return \AliyunMNS\Responses\SubscribeResponse
     */
    public function topicSubscribe($topic, $subscriptionName, $subscriptionUrl)
    {
        if (! ($topic instanceof \AliyunMNS\Topic)) {
            $topic = $this->topicObjectGet($topic);
        }
        $attributes = new SubscriptionAttributes($subscriptionName, $subscriptionUrl);
        return $topic->subscribe($attributes);
    }

    /**
     * 订阅列表
     * @param $topic
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function ListSubscription($topic)
    {
        if (! ($topic instanceof \AliyunMNS\Topic)) {
            $topic = $this->topicObjectGet($topic);
        }
        return $topic->listSubscription();
    }

    /**
     * 订阅列表
     * @param $topic
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function getSubscription($topic, $subscriptionName)
    {
        if (! ($topic instanceof \AliyunMNS\Topic)) {
            $topic = $this->topicObjectGet($topic);
        }
        return $topic->getSubscriptionAttribute($subscriptionName);
    }


    /**
     * 取消订阅主题
     *
     * @param string|\AliyunMNS\Topic $topic
     *            主题的名称或主题类
     * @param string $subscriptionName
     *            订阅识别名称
     * @throws MnsException
     * @return \AliyunMNS\Responses\UnsubscribeResponse
     */
    public function topicUnsubscribe($topic, $subscriptionName)
    {
        if (! ($topic instanceof \AliyunMNS\Topic)) {
            $topic = $this->topicObjectGet($topic);
        }
        return $topic->unsubscribe($subscriptionName);
    }

    /**
     * 主题发布消息
     *
     * @param string|\AliyunMNS\Topic $topic
     *            主题的名称或主题类
     * @param string $body
     *            发布消息的内容
     * @param string $subscriptionUrl
     *            订阅事件的回调地址
     * @throws MnsException
     * @return \AliyunMNS\Responses\PublishMessageResponse
     */
    public function topicPublishMessage($topic, $body)
    {
        $request = new PublishMessageRequest($body);
        if (! ($topic instanceof \AliyunMNS\Topic)) {

            $topic = $this->topicObjectGet($topic);
        }
        return $topic->publishMessage($request);
    }

    /**
     * 主题接收消息回调 判断http_code是否为200 如果不是200 则会调用N次后结束
     * @throws \Exception
     * @return Object 包括以下属性 TopicName SubscriptionName MessageId MessageMD5 Message
     */
    public function topicGetCallbackMsg($flag=false)
    {
        $tmpHeaders = array();
        $headers = getallheaders();
        foreach ($headers as $key => $value) {
            $key = strtolower($key);
            if (0 === strpos($key, 'x-mns-')) {
                $tmpHeaders[$key] = $value;
            }
        }
        ksort($tmpHeaders);
        $canonicalizedMNSHeaders = implode("\n", array_map(function ($v, $k) {
            return $k . ":" . $v;
        }, $tmpHeaders, array_keys($tmpHeaders)));
        //$this->logs[] = "canonicalizedMNSHeaders: $canonicalizedMNSHeaders";
        $method = $_SERVER['REQUEST_METHOD'];
        $canonicalizedResource = $_SERVER['REQUEST_URI'];
        $contentMd5 = '';
        if (array_key_exists('Content-MD5', $headers)) {
            $contentMd5 = $headers['Content-MD5'];
        } elseif (array_key_exists('Content-md5', $headers)) {
            $contentMd5 = $headers['Content-md5'];
        } elseif (array_key_exists('Content-Md5', $headers)) {
            $contentMd5 = $headers['Content-Md5'];
        }

        $contentType = '';
        if (array_key_exists('Content-Type', $headers)) {
            $contentType = $headers['Content-Type'];
        }
        if (! isset($headers['Date'], $headers['X-Mns-Signing-Cert-Url'], $headers['Authorization'])) {
            throw new \Exception('回调数据未定义 Date X-Mns-Signing-Cert-Url Authorization');
        }
        $date = isset($headers['Date']) ? $headers['Date'] : '';

        $stringToSign = strtoupper($method) . "\n" . $contentMd5 . "\n" . $contentType . "\n" . $date . "\n" . $canonicalizedMNSHeaders . "\n" . $canonicalizedResource;
        //$this->logs[] = "stringToSign: $stringToSign";

        $publicKeyURL = base64_decode($headers['X-Mns-Signing-Cert-Url']);
        $publicKey = $this->getHttpResource($publicKeyURL);
        $signature = $headers['Authorization'];
        //$this->logs[] = "publicKey:$publicKey , signature: $signature";

        $pass = $this->topicSignVerify($stringToSign, $signature, $publicKey);
        if (! $pass) {
            throw new \Exception('verify signature fail');
        }

        $content = file_get_contents("php://input");
        //$this->logs[] = "content: $content";
        if (! empty($contentMd5) && $contentMd5 != base64_encode(md5($content))) {
            throw new \Exception('md5 mismatch');
        }

        if($flag){
            return $content;
        }

        return json_decode($content, true);
    }

    /**
     * 创建队列
     *
     * @param string $name
     *            名称
     * @return \AliyunMNS\Responses\CreateQueueResponse
     */
    protected function queueCreate($name)
    {
        $request = new CreateQueueRequest($name);
        return $this->getClient()->createQueue($request);
    }

    /**
     * 队列删除
     *
     * @param string $name
     *            名称
     */
    protected function queueDelete($name)
    {
        return $this->getClient()->deleteQueue($name);
    }

    /**
     * 获取队列对象
     *
     * @param string $name
     *            名称
     * @return \AliyunMNS\Queue
     */
    protected function queueObjectGet($name)
    {
        return $this->getClient()->getQueueRef($name);
    }

    /**
     * 获取队列设置
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @throws MnsException
     * @return \AliyunMNS\Responses\GetQueueAttributeResponse
     */
    public function queueGetQueue($queue)
    {
        if (! ($queue instanceof \AliyunMNS\Queue)) {
            $queue = $this->queueObjectGet($queue);
        }
        return $queue->getAttribute();
    }

    /**
     * 队列发送消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param string $body
     *            发布消息的内容
     * @param integer $delaySeconds
     *            延迟秒数 默认为0
     * @throws MnsException
     * @return \AliyunMNS\Responses\SendMessageResponse
     */
    public function queueSendMessage($queue, $body, $delaySeconds = 0)
    {
        $request = new SendMessageRequest($body, $delaySeconds);
        if (! ($queue instanceof \AliyunMNS\Queue)) {
            $queue = $this->queueObjectGet($queue);
        }
        return $queue->sendMessage($request);
    }

    /**
     * 队列批量发送消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param [] $bodys
     *            发布消息的内容数组 stirng或SendMessageRequestItem
     * @throws MnsException
     * @return \AliyunMNS\Responses\SendMessageResponse
     */
    public function queueSendBatchMessage($queue, $bodys)
    {
        $sendMessageRequestItems = [];
        foreach ($bodys as $body) {
            if ($body instanceof SendMessageRequestItem) {
                $sendMessageRequestItems = $body;
            } else {
                $sendMessageRequestItems[] = new SendMessageRequestItem($body);
            }
        }
        $request = new BatchSendMessageRequest($sendMessageRequestItems);
        if (! ($queue instanceof \AliyunMNS\Queue)) {
            $queue = $this->queueObjectGet($queue);
        }
        return $queue->batchSendMessage($request);
    }

    /**
     * 队列接收消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param integer $waitSeconds
     *            等待秒数
     * @throws MnsException
     * @return \AliyunMNS\Responses\ReceiveMessageResponse
     */
    public function queueReceiveMessage($queue, $waitSeconds = 30)
    {
        if (! ($queue instanceof \AliyunMNS\Queue)) {
            $queue = $this->queueObjectGet($queue);
        }
        try {
            $res = $queue->receiveMessage($waitSeconds);
        } catch (QueueNotExistException $ex) {
            $res = null;
        } catch (MessageNotExistException $ex) {
            $res = null;
        }

        return $res;
    }

    /**
     * 队列批量接收消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param integer $num
     *            接收消息的数量
     * @param integer $waitSeconds
     *            等待秒数
     * @throws MnsException
     * @throws MessageNotExistException
     * @return \AliyunMNS\Responses\ReceiveMessageResponse
     */
    public function queueReceiveBatchMessage($queue, $num, $waitSeconds = 30)
    {
        if (! ($queue instanceof \AliyunMNS\Queue)) {
            $queue = $this->queueObjectGet($queue);
        }
        $request = new BatchReceiveMessageRequest($num, $waitSeconds);
        try {
            $res = $queue->batchReceiveMessage($request);
        } catch (QueueNotExistException $ex) {
            $res = null;
        } catch (MessageNotExistException $ex) {
            $res = null;
        }
        return $res;
    }

    /**
     * 删除消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param string $receiptHandle
     *            消息回执
     */
    public function queueDeleteMessage($queue, $receiptHandle)
    {
        if (! ($queue instanceof \AliyunMNS\Queue)) {
            $queue = $this->queueObjectGet($queue);
        }
        return $queue->deleteMessage($receiptHandle);
    }

    /**
     * 删除消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param [] $receiptHandles
     *            消息回执数组
     */
    public function queueDeleteBatchMessage($queue, $receiptHandles)
    {
        if (! ($queue instanceof \AliyunMNS\Queue)) {
            $queue = $this->queueObjectGet($queue);
        }
        return $queue->batchDeleteMessage($receiptHandles);
    }

    /**
     * curl get 调用
     *
     * @param string $url
     *            链接
     * @return string
     */
    protected function getHttpResource($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * 验证主题签名
     *
     * @param string $data
     *            数据
     * @param string $signature
     *            签约
     * @param string $pubKey
     *            公钥
     * @return bool
     */
    protected function topicSignVerify($data, $signature, $pubKey)
    {
        $res = openssl_get_publickey($pubKey);
        $result = (bool) openssl_verify($data, base64_decode($signature), $res);
        openssl_free_key($res);
        return $result;
    }

    private static $instance;

    private function __construct()
    {}

    /**
     * 获取当前类的一个可操作的实例
     *
     * @return \Service\MessageQueue\MNSHelper
     */
    public static function instance()
    {
        self::prepare();
        return self::$instance;
    }

    /**
     * 在提供具体操作前, 进行必要的准备工作
     */
    private static function prepare()
    {
        if (self::$instance !== null)
            return;

        $config = \Yii::$app->params[self::aliasName()];
        if(empty($config) || sizeof($config)<3)
            throw new \Exception( self::aliasName() . '类的配置不正确，至少需包含[endpoint/accessKeyId/accessKeySecret]三项配置。请核对。');
        self::$instance = new MNSHelper();
        self::$instance->setEndpoint($config['endpoint']);
        self::$instance->setAccessKeyId($config['accessKeyId']);
        self::$instance->setAccessKeySecret($config['accessKeySecret']);
        self::$instance->settopicName($config['topicName']);
        self::$instance->setsubscriptionName($config['subscriptionName']);
    }

    /**
     * 向队列发送消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param string $body
     *            发布消息的内容
     * @param integer $delaySeconds
     *            延迟秒数 默认为0
     * @throws MnsException
     * @return \AliyunMNS\Responses\SendMessageResponse
     */
    public static function sendMessage($queue, $body, $delaySeconds = 0)
    {
        self::prepare();
        return self::$instance->queueSendMessage($queue, $body, $delaySeconds);
    }

    /**
     * 向队列批量发送消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param [] $bodys
     *            发布消息的内容数组 stirng或SendMessageRequestItem
     * @throws MnsException
     * @return \AliyunMNS\Responses\SendMessageResponse
     */
    public static function sendBatchMessage($queue, $bodys)
    {
        self::prepare();
        return self::$instance->queueSendBatchMessage($queue, $bodys);
    }

    /**
     * 从队列接收消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param integer $waitSeconds
     *            等待秒数
     * @throws MnsException
     * @return \AliyunMNS\Responses\ReceiveMessageResponse
     */
    public static function receiveMessage($queue, $waitSeconds = 30)
    {
        self::prepare();
        return self::$instance->queueReceiveMessage($queue, $waitSeconds);
    }

    /**
     * 从队列批量接收消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param integer $num
     *            接收消息的数量
     * @param integer $waitSeconds
     *            等待秒数
     * @throws MnsException
     * @throws MessageNotExistException
     * @return \AliyunMNS\Responses\ReceiveMessageResponse
     */
    public static function receiveBatchMessage($queue, $num, $waitSeconds = 30)
    {
        self::prepare();
        return self::$instance->queueReceiveBatchMessage($queue, $num, $waitSeconds);
    }

    /**
     * 从队列删除消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param string $receiptHandle
     *            消息回执
     */
    public static function deleteMessage($queue, $receiptHandle)
    {
        self::prepare();
        return self::$instance->queueDeleteMessage($queue, $receiptHandle);
    }

    /**
     * 从队列批量删除消息
     *
     * @param string|\AliyunMNS\Queue $queue
     *            队列的名称或队列类
     * @param [] $receiptHandles
     *            消息回执数组
     */
    public static function deleteBatchMessage($queue, $receiptHandles)
    {
        self::prepare();
        return self::$instance->queueDeleteBatchMessage($queue, $receiptHandles);
    }

    /**
     * 订阅主题
     *
     * @param string|\AliyunMNS\Topic $topic
     *            主题的名称或主题类
     * @param string $subscriptionName
     *            订阅识别名称
     * @param string $subscriptionUrl
     *            订阅事件的回调地址
     * @throws MnsException
     * @return \AliyunMNS\Responses\SubscribeResponse
     */
    public static function subscribeTopic($topic, $subscriptionName, $subscriptionUrl)
    {
        self::prepare();
        return self::$instance->topicSubscribe($topic, $subscriptionName, $subscriptionUrl);
    }

    /**
     * 取消订阅主题
     *
     * @param string|\AliyunMNS\Topic $topic
     *            主题的名称或主题类
     * @param string $subscriptionName
     *            订阅识别名称
     * @throws MnsException
     * @return \AliyunMNS\Responses\UnsubscribeResponse
     */
    public static function unsubscribeTopic($topic, $subscriptionName)
    {
        self::prepare();
        return self::$instance->topicUnsubscribe($topic, $subscriptionName);
    }

    /**
     * 发布主题消息
     *
     * @param string|\AliyunMNS\Topic $topic
     *            主题的名称或主题类
     * @param string $body
     *            发布消息的内容
     * @param string $subscriptionUrl
     *            订阅事件的回调地址
     * @throws MnsException
     * @return \AliyunMNS\Responses\PublishMessageResponse
     */
    public static function publishTopicMessage($topic, $body)
    {
        self::prepare();
        return self::$instance->topicPublishMessage($topic, $body);
    }

    public static function topicObject($name)
    {
        self::prepare();
        return self::$instance->topicObjectGet($name);
    }

    public static function aliasName()
    {
        return 'MNSHelper';
    }
}
