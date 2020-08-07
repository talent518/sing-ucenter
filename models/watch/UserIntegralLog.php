<?php

namespace app\models\watch;

use Yii;

/**
 * This is the model class for table "user_integral_log".
 *
 * @property int $user_id 用户ID
 * @property int $periods_id 期数ID
 * @property int $dest_type 目标类型(1教材,2环节,3学习报告,4调查问卷,5生成证书,6分享证书,7礼品兑换)
 * @property int $dest_id 目标ID
 * @property int $flag 标示
 * @property int $stars 素材时长
 * @property string $remark 备注
 * @property string $created_at 创建时间
 */
class UserIntegralLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_integral_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'periods_id', 'dest_type', 'dest_id', 'flag'], 'required'],
            [['user_id', 'periods_id', 'dest_type', 'dest_id', 'flag', 'stars'], 'integer'],
            [['created_at'], 'safe'],
            [['remark'], 'string', 'max' => 100],
            [['user_id', 'periods_id', 'dest_type', 'dest_id', 'flag'], 'unique', 'targetAttribute' => ['user_id', 'periods_id', 'dest_type', 'dest_id', 'flag']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'periods_id' => '期数ID',
            'dest_type' => '目标类型(1教材,2环节,3学习报告,4调查问卷,5生成证书,6分享证书,7礼品兑换)',
            'dest_id' => '目标ID',
            'flag' => '标示',
            'stars' => '星星数',
            'remark' => '备注',
            'created_at' => '创建时间',
        ];
    }
    
    public function afterSave($insert, $changedAttributes) {
    	parent::afterSave($insert, $changedAttributes);
    	
    	$model = UserIntegral::findOne($this->user_id);
    	if(!$model) {
    		$model = new UserIntegral();
    		$model->user_id = $this->user_id;
    		$model->stars = 0;
    	}
    	$model->stars += $this->stars;
    	$model->save(false);
    }
}
