<?php
namespace app\modules\watch\business;

use app\models\watch\UserIntegral;
use app\models\watch\UserIntegralLog;

class Integral extends Base {
	
	/**
	 * 根据用户ID、期数ID和课程ID获取星星记录列表
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param array $course_id 课程ID(如果产品工具时course_id为0)
	 * @param array $business_type 业务类型(1教材 2环节 3学习报告 4调查问卷 5生成证书 6分享证书 7礼品兑换 8成长记录 9家长须知)
	 * @param array $dest_type 目标类型(1产品2课程3主题4教材5环节)
	 */
	public function course(int $user_id, int $periods_id, array $course_id = [], array $business_type = [], array $dest_type = []) {
		$query = UserIntegralLog::find()->where(compact('user_id', 'periods_id'));
		if($course_id) {
			$query->andWhere(compact('course_id'));
		}
		if($business_type) {
			$query->andWhere(compact('business_type'));
		}
		if($dest_type) {
			$query->andWhere(compact('dest_type'));
		}
		$data = $query->all();
		
		foreach($data as &$row) {
			$row = $row->attributes;
			unset($row['user_id'], $row['periods_id']);
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
	 * @param int $course_id 课程ID(如果产品工具时course_id为0)
	 * @param int $business_type 业务类型(1教材 2环节 3学习报告 4调查问卷 5生成证书 6分享证书 7礼品兑换 8成长记录 9家长须知)
	 * @param int $dest_type 目标类型(1产品2课程3主题4教材5环节)
	 * @param int $dest_id 目标ID
	 * @param int $stars 星星数
	 * @param string $remark 备注
	 * @return \app\core\CCResponse
	 */
	public function create(int $user_id, int $periods_id, int $course_id, int $business_type, int $dest_type, int $dest_id, int $stars, string $remark = '') {
		if($user_id <= 0) {
			return $this->asError('user_id参数必须是大于零的整数');
		}
		if($periods_id <= 0) {
			return $this->asError('periods_id参数必须是大于零的整数');
		}
		if($course_id < 0) {
			return $this->asError('course_id参数必须是大于等于零的整数');
		}
		if($business_type <= 0 || $business_type > 9) {
			return $this->asError('business_type不能为' . $business_type);
		}
		if($dest_type <= 0 || $dest_type > 5) {
			return $this->asError('dest_type不能为' . $dest_type);
		}
		if($dest_id <= 0) {
			return $this->asError('dest_id参数必须是大于零的整数');
		}
		
		\Yii::$app->mutex->acquire('userIntegralLock-' . $user_id, 10);
		
		$transaction = UserIntegralLog::getDb()->beginTransaction();
		
		$condition = compact('user_id', 'periods_id', 'course_id', 'business_type', 'dest_type', 'dest_id');
		$model = UserIntegralLog::findOne($condition);
		if($model) {
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