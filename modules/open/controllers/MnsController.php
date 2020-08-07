<?php
namespace app\modules\open\controllers;

use app\dicts\OrderStatus;
use app\models\UserOrder;
use Yii;
use yii\db\Transaction;
use yii\web\Controller;
use app\core\MNSHelper;
use app\core\CCRequest;
use app\models\sing\market\InvestmentPlan;

class MnsController extends Controller {

	private $code, $content, $params;
	
	private function getMNS() {
		try {
			return [204, MNSHelper::instance()->topicGetCallbackMsg(false)];
		} catch(\Error $e) {
			\Yii::error($e, 'MNSMessage');
			return [500, $e->getMessage()];
		} catch(\Exception $e) {
			\Yii::error($e, 'MNSMessage');
			return [500, $e->getMessage()];
		}
	}
	
	private function asError($data, $code) {
		$response = Yii::$app->response;
		$response->statusCode = $code;
		$response->data = $data;
		
		return $response;
	}

	public function beforeAction($action) {
		if(!parent::beforeAction($action)) {
			return false;
		}
		
		@list($this->code, $this->content) = $this->getMNS();
		
		$response = Yii::$app->response;
		$response->format = 'html';
		$response->off('beforeSend');
		$response->statusCode = 204;
		if($this->code != 204) {
			$response->statusCode = $this->code;
			return false;
		}
		
		if(isset($this->content['Message'])) {
			$this->params = json_decode(base64_decode($this->content['Message']), true);
		}

		\Yii::info($this->code, 'mns-code');
		\Yii::info($this->content, 'mns-content');
		\Yii::info($this->params, 'mns-params');
		
		return true;
	}

	public function actionTopic() {
		$id = str_replace('_', '-', $this->params['event_type'] ?? 'none');
		$action = $this->createAction($id);
		if($action === null) {
			\Yii::error('Not found action for ' . $id, 'mns');
			return $this->asError(null, 500);
		}
		try {
			return $action->runWithParams($this->params['payload'] ?? []);
		} catch(\Throwable $e) {
			$transaction = \Yii::$app->getDb()->getTransaction();
			if($transaction) {
				$transaction->rollBack();
			}
			\Yii::error($e, 'mns-error');
			return $this->asError(null, 500);
		}
	}
	
	public function actionNone() {
	}

	public function actionSsoLogout() {
	}
	
	const ORDER_TYPE_DEFAULT = 0;
	const ORDER_TYPE_YUNJI = 1;
	
	private function getOrder($id, $type) {
		\Yii::info(compact('id', 'type'), 'order-get');
		
		// 重置随机数种子。
		srand((double) microtime() * 1000000);
		$appSecret = 'ChangChang2020001'; // 开发者平台分配的 App Secret。
		$nonce = rand(); // 获取随机数。
		$timestamp = time() * 1000; // 获取时间戳（毫秒）。
		$signature = sha1($appSecret . $nonce . $timestamp);
		
		$request = new CCRequest(\Yii::$app->params['apiOrderView'] . $type . '/' . $id, 'GET', [
			'nonce' => $nonce,
			'sign_time_stamp' => $timestamp,
			'signature' => $signature
		]);
		
		$json = (($json = $request->send()) ? json_decode($json, true) : []);
		
		\Yii::info($json, 'order-view');
		if(!isset($json['code']) || $json['code'] != 200 || empty($json['data']['status'])) {
			return false;
		}
		
		return $json['data'];
	}
	
	private function toutiaoActivate($referer, $type = 3, $source = 'tp') {
		$request = new CCRequest('http://ad.toutiao.com/track/activate/', 'GET', [
			'link' => $referer,
			'source' => $source,
			'conv_time' => time(),
			'event_type' => $type
		]);
		
		$json = (($json = $request->send()) ? json_decode($json, true) : []);
		
		\Yii::info($json, 'toutiao-callback');
		if(!empty($json['code']) || empty($json['msg']) || $json['msg'] !== 'success') {
			return $this->asError(null, 500);
		}
	}
	
	private function getPlan($inviteCode) {
		return InvestmentPlan::findOne(['code'=>$inviteCode]);
	}
	
	public function actionOrderCreate($orderId, $inviteCode) {
		if(!($planModel = $this->getPlan($inviteCode))) {
			\Yii::info('不是', 'order-inviteCode');
			return;
		}
		
		$json = $this->getOrder($orderId, self::ORDER_TYPE_DEFAULT);
		if(!$json) {
			return $this->asError(null, 500);
		}
		
		if(!in_array($json['status'], [1,4,5])) {
			\Yii::info('已失败下单', 'order-status');
			return;
		}
		
		\Yii::info('已成功下单', 'order-status');
		
		if(empty($json['referer'])) {
			\Yii::info('下单引用URL为空', 'order-referer');
			return;
		}
		
		return $this->toutiaoActivate($json['referer']);
	}
	
	public function actionOrderActive($orderId, $inviteCode) {
		if(!($planModel = $this->getPlan($inviteCode))) {
			\Yii::info('不是', 'order-inviteCode');
			return;
		}
		
		$json = $this->getOrder($orderId, self::ORDER_TYPE_YUNJI);
		if(!$json) {
			return $this->asError(null, 500);
		}
		
		if(!in_array($json['status'], [1,4,5])) {
			\Yii::info('已失败下单', 'order-status');
			return;
		}
		
		\Yii::info('已成功下单', 'order-status');
		
		if(empty($json['referer'])) {
			\Yii::info('下单引用URL为空', 'order-referer');
			return;
		}
		
		return $this->toutiaoActivate($json['referer']);
	}

}
