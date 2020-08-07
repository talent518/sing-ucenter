<?php

namespace app\controllers;

use app\constants\ErrInfo;
use app\services\SSOService\SSOService;
use app\core\{CCController, CCException, CCResponse};
use app\models\LoginForm;
use yii\base\Exception;

class UserController extends CCController
{
    private $_service;

    function __construct($id, $module, $config = [])
    {
        $this->_service = new SSOService();
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['login'];
        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'login' => ['POST', 'OPTIONS'],
            'logout' => ['POST', 'OPTIONS'],
        ];
    }

    public function actionLogin()
    {
        $data = \Yii::$app->request->post();
        if (empty($data))
            throw new CCException(ErrInfo::MISS_REQUIRE_PARAMS);
        $model = new LoginForm;
        $model->setAttributes($data);
        $user = $model->login();
        if (!$user)
            throw new Exception(current($model->getFirstErrors()));
        $data = [
            'user_info' => json_decode($user->user_info, true),
            'token' => $user->token
        ];
        return new CCResponse($data);

    }

    public function actionGetPermissions()
    {
        $permissions = $this->_service->getPermissions($this->getUser()->sso_token);
        return new CCResponse($permissions);
    }

    public function actionLogout()
    {
        $user = \Yii::$app->user->getIdentity();
        \Yii::$app->user->logout();
        if ($user) {
            $user->removeAuthKey();
            $user->save();
        }
        return new CCResponse();
    }

    public function actionRetoken()
    {
        return new CCResponse();
    }
}