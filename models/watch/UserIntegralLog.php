<?php

namespace app\models\watch;

use Yii;

/**
 * This is the model class for table "user_integral_log".
 *
 * @property int $user_id 用户ID
 * @property int $periods_id 期数ID
 * @property int $class_id 班级ID
 * @property int $course_id 课程ID
 * @property int $business_type 业务类型(1教材 2环节 3学习报告 4调查问卷 5生成证书 6分享证书 7礼品兑换 8成长记录)
 * @property int $dest_type 目标类型(1产品2课程3主题4教材5环节)
 * @property int $dest_id 目标ID
 * @property int $flag 允许重复的标示
 * @property int $stars 素材时长
 * @property string $platform 平台
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
            [['user_id', 'periods_id', 'class_id', 'course_id', 'business_type', 'dest_type', 'dest_id'], 'required'],
            [['user_id', 'periods_id', 'class_id', 'course_id', 'business_type', 'dest_type', 'dest_id', 'stars', 'flag'], 'integer'],
            [['created_at'], 'safe'],
            [['remark'], 'string', 'max' => 100],
            [['platform'], 'in', 'range'=>['iphone', 'ipad', 'android', 'h5', 'mini']],
            [['user_id', 'periods_id', 'class_id', 'course_id', 'business_type', 'dest_type', 'dest_id', 'flag'], 'unique', 'targetAttribute' => ['user_id', 'periods_id', 'class_id', 'course_id', 'business_type', 'dest_type', 'dest_id', 'flag']],
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
            'class_id' => '班级ID',
            'course_id' => '课程ID',
            'business_type' => '业务类型',
            'dest_type' => '目标类型',
            'dest_id' => '目标ID',
            'flag' => '允许重复的标示',
            'stars' => '星星数',
            'platform' => '平台',
            'remark' => '备注',
            'created_at' => '创建时间',
        ];
    }
    
    public function beforeSave($insert) {
    	if(!parent::beforeSave($insert)) {
    		return false;
    	}
    	
    	return $this->stars != 0;
    }
    
    public function afterSave($insert, $changedAttributes) {
    	parent::afterSave($insert, $changedAttributes);
    	
    	$model = UserIntegral::findOne($this->user_id);
    	if(!$model) {
    		$model = new UserIntegral();
    		$model->user_id = $this->user_id;
    		$model->stars = $model->iphone_stars = $model->ipad_stars = $model->android_stars = $model->h5_stars = $model->mini_stars = 0;
    	}
    	$model->stars += $this->stars;
    	if($this->platform) {
    		$model->{$this->platform . '_stars'} += $this->stars;
    	}
    	$model->save(false);
    }
}
