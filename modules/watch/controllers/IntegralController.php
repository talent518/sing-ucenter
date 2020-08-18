<?php
namespace app\modules\watch\controllers;

use app\constants\ErrInfo;
use app\modules\watch\business\Integral;
use app\modules\watch\components\Controller;

class IntegralController extends Controller {
	
	private $integral;
	
	public function init() {
		$this->integral = new Integral();
	}
	
	protected function verbs() {
		return [
			'course' => ['GET'],
			'view' => ['GET'],
			'create' => ['POST'],
		];
	}
	
	public function actionCourse(int $user_id, int $periods_id) {
		if($user_id <= 0 || $periods_id <= 0) {
			return $this->asJson(ErrInfo::MISS_REQUIRE_PARAMS);
		}
		
		return $this->integral->course($user_id, $periods_id, $this->getParamAsArray('course_id'), $this->getParamAsArray('business_type'), $this->getParamAsArray('dest_type'));
	}
	
	public function actionView(int $id) {
		return $this->integral->view($id);
	}

	public function actionCreate(int $user_id, int $periods_id, int $course_id, int $business_type, int $dest_type, int $dest_id, int $stars, string $remark = '') {
		return $this->integral->create($user_id, $periods_id, $course_id, $business_type, $dest_type, $dest_id, $stars, $remark);
	}

}
