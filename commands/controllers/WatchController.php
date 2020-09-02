<?php
namespace app\commands\controllers;

use yii\console\Controller;
use app\models\watch\UserIntegralLog;
use yii\db\Transaction;

class WatchController extends Controller {

	public function actionIntegral() {
		$list = UserIntegralLog::find()->all();
		foreach($list as $model) {
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
				
				$exists = $db->createCommand('SELECT id FROM `sing-user`.`user_integral_report` WHERE user_id=:uid AND is_add=:is_add AND `source`=:source AND desc_id=:desc_id AND periods_id=:periods_id', [
					':uid' => $model->user_id,
					':is_add' => $model->stars > 0 ? 1 : 0,
					':source' => 200 + ($model->business_type << 5) + $model->dest_type + ($model->flag << 10),
					':desc_id' => $model->dest_id,
					':periods_id' => $model->periods_id
				])->queryScalar();
				if($exists) {
					$transaction->commit();
					continue;
				}
				
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
			} catch(\Exception $e) {
				$transaction->rollBack();
				echo $e->getMessage(), PHP_EOL;
			}
		}
	}

}