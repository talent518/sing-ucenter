<?php
namespace app\modules\watch\controllers;

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
			'view' => ['GET'],
			'create' => ['POST'],
		];
	}
	
	public function actionIndex() {
		
	}
	
	public function actionView(int $id) {
		return $this->time->view($id);
	}

	public function actionCreate(int $user_id, int $periods_id, int $class_id, int $textbook_id, int $segment_id, int $element_id, int $duration, int $play_time, int $is_complete, int $is_playable) {
		return $this->time->createOrUpdate($user_id, $periods_id, $class_id, $textbook_id, $segment_id, $element_id, $duration, $play_time, $is_complete, $is_playable);
	}

}
