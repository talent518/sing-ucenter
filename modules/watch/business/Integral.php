<?php
namespace app\modules\watch\business;

use app\models\watch\UserIntegralLog;
use yii\db\Transaction;
use app\models\watch\UserIntegral;

class Integral extends Base {
	
	/**
	 * 教材
	 * @var integer
	 */
	const DEST_TYPE_TEXTBOOK = 1;
	
	/**
	 * 环节
	 * @var integer
	 */
	const DEST_TYPE_SEGMENT = 2;

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
	 * @param int $dest_type 目标类型(1教材,2环节,3学习报告,4调查问卷,5生成证书,6分享证书,7礼品兑换)
	 * @param int $dest_id 目标ID
	 * @param int $flag 标示
	 * @param int $stars 星星数
	 * @param string $remark 备注
	 * @return \app\core\CCResponse
	 */
	public function create(int $user_id, int $periods_id, int $dest_type, int $dest_id, int $flag, int $stars, string $remark = '') {
		if($user_id <= 0) {
			return $this->asError('user_id参数必须是大于零的整数');
		}
		if($periods_id <= 0) {
			return $this->asError('periods_id参数必须是大于零的整数');
		}
		switch($dest_type) {
			case 1:
				break;
		}
		
		$transaction = UserIntegralLog::getDb()->beginTransaction(Transaction::SERIALIZABLE);
		
		$condition = compact('user_id', 'periods_id', 'dest_type', 'dest_id', 'flag');
		$model = UserIntegralLog::findOne($condition);
		if($model) {
			$transaction->rollBack();
			return $this->asError('星星记录存在');
		}
		
		$model = new UserIntegralLog();
		$model->setAttributes($condition);
		$model->stars = $stars;
		$model->remark = $remark;
		$model->save(false);
		
		$transaction->commit();
		
		return $this->asOK('记录星星成功');
	}

}