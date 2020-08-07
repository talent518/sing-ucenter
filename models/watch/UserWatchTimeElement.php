<?php

namespace app\models\watch;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "user_watch_play_time_element".
 *
 * @property int $user_id 用户ID
 * @property int $periods_id 期数ID
 * @property int $class_id 班级ID
 * @property int $textbook_id 课节ID
 * @property int $segment_id 环节ID
 * @property int $element_id 素材ID
 * @property int $duration 素材时长
 * @property int $play_time 播放时间
 * @property int $is_complete 是否已完成
 * @property int $is_playable 是否可播放
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserWatchTimeElement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_watch_time_element';
    }

    public function behaviors()
    {
        return [
            [
            	'class' => TimestampBehavior::class,
            	'value' => function(){
            		return new Expression('NOW()');
            	}
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'periods_id', 'class_id', 'textbook_id', 'segment_id', 'element_id'], 'required'],
            [['user_id', 'periods_id', 'class_id', 'textbook_id', 'segment_id', 'element_id', 'duration', 'play_time', 'is_complete', 'is_playable'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'periods_id', 'class_id', 'textbook_id', 'segment_id', 'element_id'], 'unique', 'targetAttribute' => ['user_id', 'periods_id', 'class_id', 'textbook_id', 'segment_id', 'element_id']],
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
            'textbook_id' => '教材ID',
            'segment_id' => '环节ID',
            'element_id' => '素材ID',
            'duration' => '素材时长',
            'play_time' => '播放时间',
            'is_complete' => '是否已完成',
            'is_playable' => '是否可播放',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    public function afterSave($insert, $changedAttributes) {
    	parent::afterSave($insert, $changedAttributes);
    	
    	$model = UserWatchTime::findOne($this->user_id);
    	if(!$model) {
    		$model = new UserWatchTime();
    		$model->user_id = $this->user_id;
    		$model->play_time = 0;
    	}
    	$model->play_time += $this->play_time;
    	$model->save(false);
    }
}
