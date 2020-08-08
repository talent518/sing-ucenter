<?php
namespace app\modules\watch\business;

use app\models\watch\UserWatchTime;
use app\models\watch\UserWatchTimeDate;
use app\models\watch\UserWatchTimeElement;
use yii\db\Expression;
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
		$data = UserWatchTimeElement::find()->select(new Expression('textbook_id, segment_id, SUM(IF(is_playable=0,0,play_time)) play_time, SUM(IF(play_time>=duration,1,play_time/duration)) num, COUNT(element_id) total'))->where(compact('user_id', 'periods_id', 'course_id'))->groupBy('textbook_id, segment_id')->createCommand()->queryAll();
		$ret = [];
		foreach($data as $row) {
			$ret[$row['textbook_id']][] = [
				'segment_id' => (int) $row['segment_id'],
				'play_time' => (int) $row['play_time'],
				'progress' => min($row['num'] / max(3, $row['total']), 1)
			];
		}
		$data = [];
		foreach($ret as $id=>$segs) {
			$data[] = [
				'textbook_id' => $id,
				'play_time' => array_sum(array_column($segs, 'play_time')),
				'progress' => min(array_sum(array_column($segs, 'progress')) / max(3, count($segs)) * 100, 100)
			];
		}
		return $this->asData($data);
	}
	
	/**
	 * 根据用户ID、期数ID和教材ID获取每环节进度
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $textbook_id 教材ID
	 */
	public function textbook(int $user_id, int $periods_id, int $textbook_id) {
		$data = UserWatchTimeElement::find()->select(new Expression('segment_id, SUM(IF(is_playable=0,0,play_time)) play_time, SUM(IF(play_time>=duration,1,play_time/duration)) num, COUNT(element_id) total'))->where(compact('user_id', 'periods_id', 'textbook_id'))->groupBy('segment_id')->createCommand()->queryAll();
		foreach($data as &$row) {
			$row = [
				'segment_id' => (int) $row['segment_id'],
				'play_time' => (int) $row['play_time'],
				'progress' => min(ceil($row['num'] / max(3, $row['total']) * 100), 100)
			];
		}
		return $this->asData($data);
	}
	
	/**
	 * 根据用户ID、期数ID和环节ID获取每素材进度
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $segment_id 环节ID
	 */
	public function segment(int $user_id, int $periods_id, int $segment_id) {
		$data = UserWatchTimeElement::find()->select('element_id, duration, play_time')->where(compact('user_id', 'periods_id', 'segment_id'))->all();
		foreach($data as &$row) {
			$row = [
				'element_id' => $row->element_id,
				'play_time' => $row->play_time,
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
	 * @param int $is_playable 是否可播放
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
		
		$transaction = UserWatchTimeElement::getDb()->beginTransaction(Transaction::SERIALIZABLE);
		
		$model = UserWatchTimeElement::findOne($condition);
		if(!$model) {
			$model = new UserWatchTimeElement();
			$model->setAttributes($condition);
			$model->play_time = 0;
		}
		
		$model->duration = 300; // TODO 临时处理
		$model->play_time += $play_time;
		$model->is_playable = 1; // TODO 临时处理
		$ret = $model->save(false);
		
		$transaction->commit();
		
		return $ret ? $this->asOK('记录时长成功') : $this->asError('记录时长失败');
	}

}