<?php
namespace app\modules\watch\businesses;

use app\constants\ErrInfo;
use app\core\CCException;
use app\models\watch\UserIntegral;
use app\models\watch\UserIntegralLog;
use app\modules\watch\businesses\BusinessInterface\IIntegralBusiness;
use yii\db\Transaction;

class IntegralBusiness implements IIntegralBusiness {
	
	/**
	 * 根据用户ID、期数ID和课程ID获取星星记录列表
	 * 
	 * @param int $user_id 用户ID
	 * @param int $periods_id 期数ID
	 * @param array $course_id 课程ID(如果产品工具时course_id为0)
	 * @param array $business_type 业务类型(1教材 2环节 3学习报告 4调查问卷 5生成证书 6分享证书 7礼品兑换 8成长记录 9家长须知)
	 * @param array $dest_type 目标类型(1产品2课程3主题4教材5环节)
	 * 
	 * @return array
	 */
	public function course(int $user_id, int $periods_id, array $course_id = [], array $business_type = [], array $dest_type = []) : array {
		if($user_id <= 0 || $periods_id <= 0) {
			throw new CCException(ErrInfo::MISS_REQUIRE_PARAMS);
		}
		
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
			if($row['platform'] === null) {
				$row['platform'] = '';
			}
			unset($row['user_id'], $row['periods_id']);
		}
		
		return $data;
	}

	/**
	 * 查询用户星星数
	 * 
	 * @param int $user_id 用户ID
	 * @return int
	 */
	public function view(int $user_id) : int {
		if($user_id <= 0) {
			throw new CCException(ErrInfo::MISS_REQUIRE_PARAMS, 'user_id');
		}
		
		return (int) UserIntegral::find()->select('stars')->where(compact('user_id'))->scalar();
	}
	
	/**
	 * 查询用户星星数
	 * 
	 * @param int $user_id 用户ID
	 * @return int
	 */
	public function viewMerge(int $user_id) : int {
		if($user_id <= 0) {
			throw new CCException(ErrInfo::MISS_REQUIRE_PARAMS, 'user_id');
		}
		
		return (int) \Yii::$app->db->createCommand('SELECT last_value FROM `sing-user`.`user_integral_report` WHERE user_id=:uid ORDER BY id DESC LIMIT 1', [':uid'=>$user_id])->queryScalar();
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
	 * @param int $duplicates 允许重复次数
	 * @param string $platform 平台：iphone, ipad, android, h5, mini
	 * @return bool
	 */
	public function create(int $user_id, int $periods_id, int $course_id, int $business_type, int $dest_type, int $dest_id, int $stars, string $remark = '', int $duplicates = 0, string $platform = '') : bool {
		if($user_id <= 0) {
			throw new CCException(ErrInfo::INVALID_PARAMS, 'user_id参数必须是大于零的整数');
		}
		if($periods_id <= 0) {
			throw new CCException(ErrInfo::INVALID_PARAMS, 'periods_id参数必须是大于零的整数');
		}
		if($course_id < 0) {
			throw new CCException(ErrInfo::INVALID_PARAMS, 'course_id参数必须是大于等于零的整数');
		}
		if($business_type <= 0 || $business_type > 9) {
			throw new CCException(ErrInfo::INVALID_PARAMS, 'business_type不能为' . $business_type);
		}
		if($dest_type <= 0 || $dest_type > 5) {
			throw new CCException(ErrInfo::INVALID_PARAMS, 'dest_type不能为' . $dest_type);
		}
		if($dest_id <= 0) {
			throw new CCException(ErrInfo::INVALID_PARAMS, 'dest_id参数必须是大于零的整数');
		}
		
		if($stars == 0) {
			throw new CCException(ErrInfo::INVALID_PARAMS, 'stars参数不能为0');
		}
		
		\Yii::$app->mutex->acquire('userIntegralLock-' . $user_id, 10);
		
		$transaction = UserIntegralLog::getDb()->beginTransaction();
		
		if($stars < 0 && UserIntegral::find()->where(compact('user_id'))->select('stars')->scalar() < -$stars) {
			throw new CCException(ErrInfo::INVALID_PARAMS, '星星数不够');
		}
		
		$condition = compact('user_id', 'periods_id', 'course_id', 'business_type', 'dest_type', 'dest_id');
		if($duplicates > 0) {
			$condition['flag'] = (int) UserIntegralLog::find()->where($condition)->max('flag') + 1;
			if($condition['flag'] > $duplicates) {
				return false;
			}
		} elseif(UserIntegralLog::find()->where($condition)->exists()) {
			return false;
		} else {
			$condition['flag'] = 0;
		}
		
		$model = new UserIntegralLog();
		$model->setAttributes($condition);
		$model->stars = $stars;
		$model->remark = $remark;
		if(in_array($platform, ['iphone', 'ipad', 'android', 'h5', 'mini'])) {
			$model->platform = $platform;
		}
		$ret = $model->save(false);
		$transaction->commit();
		
		if(!$ret) {
			throw new CCException(ErrInfo::SAVE_FAILURE, '记录星星失败');
		}
		
		$desc = '学习奖励';
		switch ($model->business_type) {
			case 9:
				$desc = '完成家长须知';
				break;
			case 3:
				$desc = '分享学习报告';
				break;
			case 5: 
				$desc = '生成毕业证书';
				break;
			case 6: 
				$desc = '分享毕业证书';
				break;
		}
		
		try {
			$db = \Yii::$app->getDb();
			$transaction = $db->beginTransaction(Transaction::SERIALIZABLE);
			$lastValue = $db->createCommand('SELECT last_value FROM `sing-user`.`user_integral_report` WHERE user_id=:uid ORDER BY id DESC', [
				':uid' => $model->user_id
			])->queryScalar();
			$params = [
				'user_id' => $model->user_id,
				'is_add' => $model->stars > 0 ? 1 : 0,
				'value' => abs($model->stars),
				'source' => 200 + ($model->business_type << 5) + $model->dest_type + ($model->flag << 10),
				'last_value' => $lastValue + $model->stars,
				'desc' => $desc,
				'desc_id' => $model->dest_id,
				'periods_id' => $model->periods_id
			];
			$db->createCommand('INSERT INTO `sing-user`.`user_integral_report` (`' . implode('`,`', array_keys($params)) . '`,`created_at`,`updated_at`)VALUES(:' . implode(',:', array_keys($params)) . ',NOW(),NOW())', $params)->execute();
			$transaction->commit();
			
			return true;
		} catch(\Exception $e) {
			$transaction->rollBack();
			throw new CCException(ErrInfo::SYSTEM_ERROR, $e->getMessage(), $e);
		}
	}

}