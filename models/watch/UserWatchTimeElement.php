<?php

namespace app\models\watch;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model course for table "user_watch_play_time_element".
 *
 * @property int $user_id 用户ID
 * @property int $periods_id 期数ID
 * @property int $course_id 课程ID
 * @property int $textbook_id 课节ID
 * @property int $segment_id 环节ID
 * @property int $element_id 素材ID
 * @property int $duration 素材时长
 * @property int $play_time 播放时间
 * @property int $is_playable 是否可播放
 * @property int $max_play_time 最大播放时长
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
            [['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id', 'element_id'], 'required'],
            [['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id', 'element_id', 'duration', 'play_time', 'is_playable', 'max_play_time'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id', 'element_id'], 'unique', 'targetAttribute' => ['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id', 'element_id']],
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
            'course_id' => '课程ID',
            'textbook_id' => '教材ID',
            'segment_id' => '环节ID',
            'element_id' => '素材ID',
            'duration' => '素材时长',
            'play_time' => '播放时间',
            'is_playable' => '是否可播放',
            'max_play_time' => '最大播放时长',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    private $_play_time = 0;
    public function afterFind() {
    	parent::afterFind();
    	
    	$this->_play_time = $this->play_time;
    }
    
    public function beforeSave($insert) {
    	if(!parent::beforeSave($insert)) {
    		return false;
    	}
    	
    	return PHP_SAPI === 'cli' || $this->play_time > $this->_play_time;
    }
    
    public function afterSave($insert, $changedAttributes) {
    	parent::afterSave($insert, $changedAttributes);
    	
    	$playTime = ($this->play_time - $this->_play_time);
    	
    	if($this->is_playable) {
    		$model = UserWatchTime::findOne($this->user_id);
    		if(!$model) {
    			$model = new UserWatchTime();
    			$model->user_id = $this->user_id;
    			$model->play_time = 0;
    		}
    		$model->play_time += $playTime;
    		$model->save(false);
    	
    		$date = date('Y-m-d');
    		$model = UserWatchTimeDate::findOne(['user_id'=>$this->user_id, 'date'=>$date]);
    		if(!$model) {
    			$model = new UserWatchTimeDate();
    			$model->user_id = $this->user_id;
    			$model->date = $date;
    			$model->play_time = 0;
    		}
    		$model->play_time += $playTime;
    		$model->save(false);
    	}
    	
    	$attrs = $this->getAttributes(['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id']);
    	$model = UserWatchTimeSegment::findOne($attrs);
    	if(!$model) {
    		$model = new UserWatchTimeSegment();
    		$model->attributes = $attrs;
    	}
    	$model->save(false);
    }
}
