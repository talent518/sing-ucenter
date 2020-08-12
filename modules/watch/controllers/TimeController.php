<?php
namespace app\modules\watch\controllers;

use app\constants\ErrInfo;
use app\modules\watch\business\Time;
use app\modules\watch\components\Controller;

class TimeController extends Controller {
	
	private $time;
	
	public function init() {
		$this->time = new Time();
	}
	
	protected function verbs() {
		return [
			'index' => ['GET'],
			'get-attr' => ['GET'],
			'view' => ['GET'],
			'create' => ['POST'],
		];
	}
	
	public function actionCourse(int $user_id, int $periods_id, int $course_id) {
		if($user_id <= 0 || $periods_id <= 0 || $course_id <= 0) {
			return $this->asJson(ErrInfo::MISS_REQUIRE_PARAMS);
		}
		
		return $this->time->course($user_id, $periods_id, $course_id);
	}
	
	public function actionTextbook(int $user_id, int $periods_id, int $course_id, int $textbook_id) {
		if($user_id <= 0 || $periods_id <= 0 || $course_id <= 0 || $textbook_id <= 0) {
			return $this->asJson(ErrInfo::MISS_REQUIRE_PARAMS);
		}
		
		return $this->time->textbook($user_id, $periods_id, $course_id, $textbook_id);
	}
	
	public function actionSegment(int $user_id, int $periods_id, int $course_id, int $textbook_id, int $segment_id) {
		if($user_id <= 0 || $periods_id <= 0 || $course_id <= 0 || $textbook_id <= 0 || $segment_id <= 0) {
			return $this->asJson(ErrInfo::MISS_REQUIRE_PARAMS);
		}
		
		return $this->time->segment($user_id, $periods_id, $course_id, $textbook_id, $segment_id);
	}
	
	public function actionGetAttr($id, $attr) {
		if($attr === 'everyday') {
			return $this->time->everyday($id);
		} elseif($attr === 'today') {
			return $this->time->today($id);
		}
	}
	
	public function actionView(int $id) {
		return $this->time->view($id);
	}

	public function actionCreate(int $user_id, int $periods_id, int $course_id, int $textbook_id, int $segment_id, int $element_id, int $play_time) {
		return $this->time->createOrUpdate($user_id, $periods_id, $course_id, $textbook_id, $segment_id, $element_id, $play_time);
	}

}
