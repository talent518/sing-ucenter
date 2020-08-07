<?php

namespace app\core;


use yii\base\ActionFilter;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class HttpLogger extends ActionFilter
{

    public function beforeAction($action)
    {
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        if (!\Yii::$app->request->isOptions) {
            $costTime = \Yii::getLogger()->getElapsedTime();
            $message = [
                'slow' => false,
                'cost_time' => $costTime
            ];
            if (is_object($result)) {
                $message['response_body'] = Json::encode($result);
            } else {
                $message['response_body'] = VarDumper::export($result);
            }
            if (strlen($message['response_body']) >= 1000)
                $message['response_body'] = substr($message['response_body'], 0, 1000);
            if ($costTime >= \Yii::$app->params['execute_slow_time']) {
                $message['slow'] = true;
                \Yii::warning($message);
            } else {
                \Yii::info($message);
            }
        }
        return parent::afterAction($action, $result); // TODO: Change the autogenerated stub
    }
}