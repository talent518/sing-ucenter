<?php
namespace app\modules\watch\components;

use app\core\CCResponse;

class Controller extends \yii\rest\Controller {
	
	public function actions() {
		return [
			'options' => [
				'class' => 'yii\rest\OptionsAction'
			]
		];
	}
	
	public function beforeAction($action) {
		$request = \Yii::$app->getRequest();
		$request->enableCsrfValidation = false;
		
		if(!parent::beforeAction($action))
			return false;
		
		$headers = \Yii::$app->getResponse()->getHeaders();
		$_headers = [
			'Access-Control-Allow-Origin'=>['*'],
			'Access-Control-Allow-Methods' => ['GET,POST,PUT,PATCH,DELETE'],
			'Access-Control-Allow-Headers' => ['Origin, X-Requested-With, Content-Type, Accept, Authorization, WWW-Authorization'],
		];
		foreach($_headers as $key=>$val) {
			$headers->set($key, $val);
		}
		
		if(!in_array($request->method, ['GET','POST','PUT','PATCH','DELETE'])) {
			return false;
		}
		
		$response = \Yii::$app->getResponse();
		$response->on('beforeSend', function () use($response) {
			if($response->data instanceof CCResponse) {
				$response->format = 'json';
			}
		});
		
		return true;
	}
	
	public function bindActionParams($action, $params) {
		return parent::bindActionParams($action, $params + \Yii::$app->request->bodyParams);
	}
	
	private static $params = [];
	
	protected function getParam($key, $def = null) {
		if(array_key_exists($key, self::$params)) {
			return self::$params[$key];
		}
		$request = \Yii::$app->request;
		return self::$params[$key] = $request->getBodyParam($key, $request->get($key, $def));
	}
	
	protected function getParamAsArray(string $name, string $split = ',') {
		$arr = $this->getParam($name, []);
		if(! is_array($arr)) {
			if(is_string($arr) && $arr !== '') {
				$arr = explode($split, $arr);
			} else {
				$arr = [];
			}
		}
		return $arr;
	}
	
	protected function hasParam($key) {
		$request = \Yii::$app->request;
		
		return array_key_exists($key, $request->queryParams) || array_key_exists($key, $request->bodyParams);
	}
	
}