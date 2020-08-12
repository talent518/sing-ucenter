<?php
namespace app\commands\controllers;

use app\models\watch\UserWatchTimeElement;
use app\services\watch\CourseService;
use yii\console\Controller;
use yii\db\Transaction;

class WatchController extends Controller {
	public function actionRefresh() {
		$userIds = UserWatchTimeElement::find()->distinct()->select('user_id')->column();
		foreach($userIds as $user_id) {
			\Yii::$app->mutex->acquire('userWatchTimeLock-' . $user_id, 10);
			$list = UserWatchTimeElement::findAll(compact('user_id'));
			foreach($list as $i=>$model) { /* @var $model UserWatchTimeElement */
				echo $i+1, ' - Element(', implode(', ', array_map(function($key) use($model) {
						return $key . ': ' . $model->$key;
					}, ['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id', 'element_id'])), ') ... ';
				
				$element = CourseService::getElement($model->course_id, $model->textbook_id, $model->segment_id, $model->element_id);
				if(!$element) {
					echo 'Not Found', PHP_EOL;
					$model->delete();
					$model->afterSave(false, []);
					continue;
				}
				
				$model->is_playable = ($element['is_play'] ? 1 : 0);
				$model->duration = ($element['is_play'] ? max(floor($element['duration']), 1) : 1);
				$ret = $model->save(false);
				
				echo $ret ? 'Success' : 'Failure', PHP_EOL;
			}
			\Yii::$app->mutex->release('userWatchTimeLock-' . $user_id);
		}
	}
	
	public function actionLock($name, $seconds=10) {
		\Yii::$app->mutex->acquire($name, $seconds);
		echo microtime(true), PHP_EOL;
		$seconds > 0 and sleep($seconds);
		echo microtime(true), PHP_EOL;
		\Yii::$app->mutex->release($name);
	}
}