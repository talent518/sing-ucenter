<?php


namespace app\core;


class CCOpenController extends CCController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
//        $behaviors['open_auth'] = [
//            'class' => OpenApiAuth::className(),
//        ];
        return $behaviors;
    }
}