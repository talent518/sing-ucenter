<?php

namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public $layout = false;

    public function actionIndex()
    {
        return $this->renderContent('<div>ucenter api,env is ' . YII_ENV . '</div>');
    }
}