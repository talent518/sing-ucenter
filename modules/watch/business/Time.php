<?php
namespace app\modules\watch\business;

use app\models\watch\UserWatchTime;
use app\models\watch\UserWatchTimeDate;
use app\models\watch\UserWatchTimeElement;
use app\models\watch\UserWatchTimeSegment;
use app\models\watch\UserWatchTimeTextbook;
use app\services\watch\CourseService;
use yii\db\Transaction;

class Time extends Base {

	/**
	 * 根据用户ID、期数ID和课程ID获取每教材进度
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $course_id 课程ID
	 */
	public function course(int $user_id, int $periods_id, int $course_id) {
		$data = UserWatchTimeTextbook::find()->select('textbook_id, progress, play_time')->where(compact('user_id', 'periods_id', 'course_id'))->all();
		foreach($data as &$row) {
			$row = $row->getAttributes(['textbook_id', 'progress', 'play_time']);
		}
		return $this->asData($data);
	}
	
	/**
	 * 根据用户ID、期数ID和教材ID获取每环节进度
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $course_id 课程ID
	 * @param int $textbook_id 教材ID
	 */
	public function textbook(int $user_id, int $periods_id, int $course_id, int $textbook_id) {
		$data = UserWatchTimeSegment::find()->select('segment_id, progress, play_time')->where(compact('user_id', 'periods_id', 'course_id', 'textbook_id'))->all();
		foreach($data as &$row) {
			$row = $row->getAttributes(['segment_id', 'progress', 'play_time']);
		}
		return $this->asData($data);
	}
	
	/**
	 * 根据用户ID、期数ID和环节ID获取每素材进度
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $course_id 课程ID
	 * @param int $textbook_id 教材ID
	 * @param int $segment_id 环节ID
	 */
	public function segment(int $user_id, int $periods_id, int $course_id, int $textbook_id, int $segment_id) {
		$data = UserWatchTimeElement::find()->select('element_id, duration, play_time, is_playable, max_play_time')->where(compact('user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id'))->all();
		foreach($data as &$row) {
			$row = [
				'element_id' => $row->element_id,
				'is_playable' => $row->is_playable,
				'play_time' => $row->play_time,
				'max_play_time' => $row->max_play_time,
				'progress' => min(ceil($row->play_time / $row->duration * 100), 100)
			];
		}
		return $this->asData($data);
	}
	
	/**
	 * 查询用户看课总时长
	 * 
	 * @param int $user_id
	 * @return \app\core\CCResponse
	 */
	public function view(int $user_id) {
		if($user_id <= 0) return $this->asData(0);
		
		return $this->asData((int) UserWatchTime::find()->select('play_time')->where(compact('user_id'))->scalar());
	}
	
	/**
	 * 查询用户每天看课时长
	 * 
	 * @param int $user_id
	 */
	public function everyday(int $user_id) {
		if($user_id <= 0) return $this->asData([]);
		
		$ret = UserWatchTimeDate::find()->select('play_time')->where(compact('user_id'))->indexBy('date')->column();
		
		foreach($ret as &$time) {
			$time = (int) $time;
		}
		
		return $this->asData($ret);
	}
	
	/**
	 * 查询用户当天看课时长
	 * 
	 * @param int $user_id
	 */
	public function today(int $user_id) {
		if($user_id <= 0) return $this->asData([]);
		
		$date = date('Y-m-d');
		return $this->asData((int) UserWatchTimeDate::find()->select('play_time')->where(compact('user_id', 'date'))->scalar());
	}

	/**
	 * 记录用户看课时长：以最小粒度 用户ID和素材ID 记录时长
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $course_id 课程ID
	 * @param int $textbook_id 教材ID
	 * @param int $segment_id 环节ID
	 * @param int $element_id 素材ID
	 * @param int $play_time 播放时间
	 * @return \app\core\CCResponse
	 */
	public function createOrUpdate(int $user_id, int $periods_id, int $course_id, int $textbook_id, int $segment_id, int $element_id, int $play_time) {
		$condition = compact('user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id', 'element_id');
		foreach($condition as $key => $val) {
			if($val <= 0) {
				return $this->asError($key . '参数必须是大于零的整数');
			}
		}
		
		if($play_time <= 0) {
			return $this->asError('play_time参数必须是大于零的整数');
		}
		
		$element = CourseService::getElement($course_id, $textbook_id, $segment_id, $element_id);
		if(!$element) {
			return $this->asError('素材不存在');
		}
		
		$transaction = UserWatchTimeElement::getDb()->beginTransaction(Transaction::SERIALIZABLE);
		
		$model = UserWatchTimeElement::findOne($condition);
		if(!$model) {
			$model = new UserWatchTimeElement();
			$model->setAttributes($condition);
			$model->play_time = 0;
		}
		
		$model->play_time += $play_time;
		
		$model->is_playable = ($element['is_play'] ? 1 : 0);
		$model->duration = ($element['is_play'] ? max(floor($element['duration']), 1) : 1);
		$model->max_play_time = max($play_time, $model->max_play_time);
		
		$ret = $model->save(false);
		
		$progress = UserWatchTimeSegment::find()->select('progress')->where(compact('user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id'))->scalar();
		
		$transaction->commit();
		
		return $ret ? $this->asData($progress ? (int) $progress : 0, '记录时长成功') : $this->asError('记录时长失败');
	}

}