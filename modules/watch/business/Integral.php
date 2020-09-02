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
	 * 查询用户星星数
	 * 
	 * @param int $user_id 用户ID
	 * @return \app\core\CCResponse
	 */
	public function viewMerge(int $user_id) {
		if($user_id <= 0) return $this->asData(0);
		
		return $this->asData((int) \Yii::$app->db->createCommand('SELECT last_value FROM `sing-user`.`user_integral_report` WHERE user_id=:uid ORDER BY id DESC LIMIT 1', [':uid'=>$user_id])->queryScalar());
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
	 * @return \app\core\CCResponse
	 */
	public function create(int $user_id, int $periods_id, int $course_id, int $business_type, int $dest_type, int $dest_id, int $stars, string $remark = '', int $duplicates = 0) {
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
		
		if($stars == 0) {
			return $this->asError('stars参数不能为0');
		}
		
		\Yii::$app->mutex->acquire('userIntegralLock-' . $user_id, 10);
		
		$transaction = UserIntegralLog::getDb()->beginTransaction();
		
		if($stars < 0 && UserIntegral::find()->where(compact('user_id'))->select('stars')->scalar() < -$stars) {
			return $this->asError('星星数不够');
		}
		
		$condition = compact('user_id', 'periods_id', 'course_id', 'business_type', 'dest_type', 'dest_id');
		if($duplicates > 0) {
			$condition['flag'] = (int) UserIntegralLog::find()->where($condition)->max('flag') + 1;
			if($condition['flag'] > $duplicates) {
				return $this->asError('超出重复次数限制');
			}
		} elseif(UserIntegralLog::find()->where($condition)->exists()) {
			return $this->asError('星星记录存在');
		} else {
			$condition['flag'] = 0;
		}
		
		$model = new UserIntegralLog();
		$model->setAttributes($condition);
		$model->stars = $stars;
		$model->remark = $remark;
		$ret = $model->save(false);
		$transaction->commit();
		
		if($ret) {
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
					'value' => $model->stars,
					'source' => 200 + $model->business_type * 32 + $model->dest_type,
					'last_value' => $lastValue + $model->stars,
					'desc' => $desc,
					'desc_id' => $model->dest_id,
					'periods_id' => $model->periods_id
				];
				$db->createCommand('INSERT INTO `sing-user`.`user_integral_report` (`' . implode('`,`', array_keys($params)) . '`,`created_at`,`updated_at`)VALUES(:' . implode(',:', array_keys($params)) . ',NOW(),NOW())', $params)->execute();
				$transaction->commit();
				
				return $this->asOK('记录星星成功');
			} catch(\Exception $e) {
				$transaction->rollBack();
				\Yii::error($e);
				return $this->asData($e->getMessage(), '记录星星成功');
			}
		} else {
			return $this->asError('记录星星失败');
		}
	}

}