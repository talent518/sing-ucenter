<?php
namespace app\modules\watch\business;

use app\models\watch\UserIntegral;
use app\models\watch\UserIntegralLog;
use yii\db\Transaction;

class Integral extends Base {
	
	/**
	 * 根据用户ID、期数ID和课程ID获取星星记录列表
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $course_id 课程ID
	 */
	public function course(int $user_id, int $periods_id, int $course_id, array $dest_type = []) {
		$query = UserIntegralLog::find()->where(compact('user_id', 'periods_id', 'course_id'));
		if($dest_type) {
			$query->andWhere(compact('dest_type'));
		}
		$data = $query->all();
		
		foreach($data as &$row) {
			$row = $row->attributes;
			unset($row['user_id'], $row['periods_id'], $row['course_id']);
		}
		
		return $this->asData($data);
	}

	/**
	 * 查询用户星星数
	 * 
	 * @param int $user_id 用户ID
	 * @return \app\core\CCResponse
	 */
	public function view(int $user_id) {
		if($user_id <= 0) return $this->asData(0);
		
		return $this->asData((int) UserIntegral::find()->select('stars')->where(compact('user_id'))->scalar());
	}

	/**
	 * 记录星星明细并自动更新用户星星数
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param int $course_id 课程ID
	 * @param int $dest_type 目标类型(1教材,2环节,3学习报告,4调查问卷,5生成证书,6分享证书,7礼品兑换)
	 * @param int $dest_id 目标ID
	 * @param int $flag 标示
	 * @param int $stars 星星数
	 * @param string $remark 备注
	 * @return \app\core\CCResponse
	 */
	public function create(int $user_id, int $periods_id, int $course_id, int $dest_type, int $dest_id, int $flag, int $stars, string $remark = '') {
		if($user_id <= 0) {
			return $this->asError('user_id参数必须是大于零的整数');
		}
		if($periods_id <= 0) {
			return $this->asError('periods_id参数必须是大于零的整数');
		}
		if($course_id <= 0) {
			return $this->asError('course_id参数必须是大于零的整数');
		}
		switch($dest_type) {
			case 1: // 教材
			case 2: // 环节
			case 3: // 学习报告
			case 4: // 调查问卷
			case 5: // 生成证书
			case 6: // 分享证书
			case 7: // 礼品兑换
				if($dest_id <= 0) {
					return $this->asError('dest_id参数必须是大于零的整数');
				}
				break;
			default:
				return $this->asError('dest_type不能为' . $dest_type);
		}
		
		$transaction = UserIntegralLog::getDb()->beginTransaction(Transaction::SERIALIZABLE);
		
		$condition = compact('user_id', 'periods_id', 'course_id', 'dest_type', 'dest_id', 'flag');
		$model = UserIntegralLog::findOne($condition);
		if($model) {
			$transaction->rollBack();
			return $this->asError('星星记录存在');
		}
		
		$model = new UserIntegralLog();
		$model->setAttributes($condition);
		$model->stars = $stars;
		$model->remark = $remark;
		$ret = $model->save(false);
		
		$transaction->commit();
		
		return $ret ? $this->asOK('记录星星成功') : $this->asError('记录星星失败');
	}

}