<?php
namespace app\core;

use EasyWeChat\Factory;

class MPHelper
{
    /**
     * @var string
     */
    private $app_id;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $mp_ads;

    /**
     * @var Object
     */
    private $client;

    public function setAppId($value)
    {
        $this->app_id = $value;
    }

    public function setSecret($value)
    {
        $this->secret = $value;
    }

    public function setMpAds($value)
    {
        $this->mp_ads = $value;
    }

    public function getMpAds()
    {
        return $this->mp_ads;
    }

    /**
     * 获取client
     * @return Client
     */
    public function getClient($reset = false)
    {
        if (! $this->client || $reset) {
            $config = [
                'app_id' => $this->app_id,
                'secret' => $this->secret,
                'response_type' => 'array',
            ];
            $this->client = Factory::officialAccount($config);
        }
        return $this->client;
    }

    private static $instance;

    private function __construct(){}

    public static function instance($appid='')
    {
        self::prepare($appid);
        return self::$instance;
    }


    private static function prepare($appid)
    {
        if (self::$instance !== null){
            return;
        }
        $config = \Yii::$app->params[self::aliasName()]['wechat'];
        if(empty($config)){
            throw new \Exception( self::aliasName() . '类的配置不正确，至少需包含[app_id/secret]二项配置。请核对。');
        }
        $config=array_combine(array_column($config, 'app_id'), $config);
        if(empty($config[$appid])){
            throw new \Exception( '该appid：'.$appid . '的配置没有找到。请核对。');
        }
        self::$instance = new MPHelper();
        self::$instance->setAppId($config[$appid]['app_id']);
        self::$instance->setSecret($config[$appid]['secret']);
        self::$instance->setMpAds($config[$appid]['mp_ads']);
    }

    public static function aliasName()
    {
        return 'MPHelper';
    }

    protected function getToken()
    {
        $tokan=$this->getClient()->access_token->getToken(false);
        return $tokan['access_token'];
    }

    /**
     * 创建数据源
     */
    public static function CreateWxReservation($name, $description)
    {

        $url = \Yii::$app->params[self::aliasName()]['action_sets_url']."&access_token=" . self::$instance->getToken();

        $data = [
            'type' => 'WEB',
            'name' => $name,
            'description' => $description
        ];

        $request = new CCRequest($url, 'POST', [], [], $data, CCRequest::CONTENT_TYPE_JSON);
        $result = $request->send();

        return $result;
    }

    /*
     * 回传数据
     */
    public static function pushOrderToWxReservation($click_id, $retry = 0)
    {
        $url = \Yii::$app->params[self::aliasName()]['actions_url']."&access_token=" . self::$instance->getToken();

        $data = [
            'actions' => [
                [
                    "user_action_set_id" => self::$instance->mp_ads['user_action_set_id'],
                    "url" => 'https://wechat.changchangenglish.com',
                    "action_time" => time(),
                    "action_type" => "COMPLETE_ORDER",
                    "trace" => [
                        "click_id" => $click_id,
                    ]
                ]
            ]
        ];

        $request = new CCRequest($url, 'POST', [], [], $data, CCRequest::CONTENT_TYPE_JSON);;
        $result = json_decode($request->send(), true);

        if ($result['errcode'] != 0 && $retry < 3) {
            return self::pushOrderToWxReservation($click_id, $retry + 1);
        }

        return $result;
    }
}
