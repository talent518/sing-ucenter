<?php

namespace app\core;

use yii\mutex\Mutex;

class App
{

    /**
     * 获取进程锁
     * @return Mutex
     */
    public static function getLockComponent()
    {
        return \Yii::$app->mutex;
    }

    public static function useSSODevMode(): bool
    {
        if (!isset(\Yii::$app->params['app_url']))
            return false;
        $referer = \Yii::$app->request->headers['Referer'];
        if (strpos($referer, \Yii::$app->params['app_url']) !== false)
            return false;
        return true;
    }
}