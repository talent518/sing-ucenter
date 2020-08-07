<?php
namespace app\modules\watch\business;

use app\models\watch\UserWatchTimeElement;
use yii\db\Transaction;
use app\models\watch\UserWatchTime;

class Time extends Base {

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
	 * 记录用户看课时长：以最小粒度 用户ID和素材ID 记录时长
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $class_id 班级ID
	 * @param int $textbook_id 教材ID
	 * @param int $segment_id 环节ID
	 * @param int $element_id 素材ID
	 * @param int $duration 素材时长
	 * @param int $time 播放时间
	 * @param int $is_complete 是否已完成
	 * @param int $is_playable 是否可播放
	 * @return \app\core\CCResponse
	 */
	public function createOrUpdate(int $user_id, int $periods_id, int $class_id, int $textbook_id, int $segment_id, int $element_id, int $duration, int $play_time, int $is_complete, int $is_playable) {
		$condition = compact('user_id', 'periods_id', 'class_id', 'textbook_id', 'segment_id', 'element_id');
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
			$model->time = 0;
		}
		
		$model->duration = $duration;
		$model->play_time += $play_time;
		$model->is_complete = $is_complete;
		$model->is_playable = $is_playable;
		$model->save(false);
		
		$transaction->commit();
		
		return $this->asOK('记录时长成功');
	}

}