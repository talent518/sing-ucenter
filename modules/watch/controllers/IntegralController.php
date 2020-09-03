<?php
namespace app\modules\watch\controllers;

use app\core\CCController;
use app\core\CCResponse;
use app\modules\watch\businesses\BusinessInterface\IIntegralBusiness;

class IntegralController extends CCController {
	
	private $integral;
	
	public function __construct($id, $module, IIntegralBusiness $integral, $config = []) {
		$this->integral = $integral;
		parent::__construct($id, $module, $config);
	}
	
	public function behaviors() {
		$behaviors = parent::behaviors();
		unset($behaviors['authenticator']);
		return $behaviors;
	}
	
	protected function verbs() {
		return [
			'course' => ['GET'],
			'view' => ['GET'],
			'create' => ['POST'],
		];
	}
	
	protected function getParamAsArray(string $name, string $split = ',') {
		$request = \Yii::$app->request;
		$arr = $request->getBodyParam($name, $request->get($name, []));
		if(! is_array($arr)) {
			if(is_string($arr) && $arr !== '') {
				$arr = explode($split, $arr);
			} else {
				$arr = [];
			}
		}
		return $arr;
	}
	
	public function actionCourse(int $user_id, int $periods_id) {
		return new CCResponse($this->integral->course($user_id, $periods_id, $this->getParamAsArray('course_id'), $this->getParamAsArray('business_type'), $this->getParamAsArray('dest_type')));
	}
	
	public function actionView(int $id) {
		return new CCResponse($this->integral->viewMerge($id));
	}

	public function actionCreate(int $user_id, int $periods_id, int $course_id, int $business_type, int $dest_type, int $dest_id, int $stars, string $remark = '', int $duplicates = 0, string $platform = '') {
		$ret = $this->integral->create($user_id, $periods_id, $course_id, $business_type, $dest_type, $dest_id, $stars, $remark, $duplicates, $platform);
		
		return new CCResponse($ret, 0, $ret ? 'ok': ($duplicates > 0 ? '超出重复次数限制' : '星星记录已存在'));
	}

}
