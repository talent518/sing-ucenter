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
			'index' => ['GET'],
			'create' => ['POST'],
		];
	}
	
	public function actionCourse(int $user_id, int $periods_id, int $course_id) {
		if($user_id <= 0 || $periods_id <= 0 || $course_id <= 0) {
			return $this->asJson(ErrInfo::MISS_REQUIRE_PARAMS);
		}
		
		return $this->integral->course($user_id, $periods_id, $course_id, $this->getParamAsArray('dest_type'));
	}
	
	public function actionView(int $id) {
		return $this->integral->view($id);
	}

	public function actionCreate(int $user_id, int $periods_id, int $course_id, int $dest_type, int $dest_id, int $flag, int $stars, string $remark = '') {
		return $this->integral->create($user_id, $periods_id, $course_id, $dest_type, $dest_id, $flag, $stars, $remark);
	}

}
