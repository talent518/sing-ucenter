<?php

namespace app\models\watch;

use app\services\watch\CourseService;
use Yii;

/**
 * This is the model class for table "user_watch_time_segment".
 *
 * @property int $user_id 用户ID
 * @property int $periods_id 期数ID
 * @property int $course_id 课程ID
 * @property int $textbook_id 教材ID
 * @property int $segment_id 环节ID
 * @property int $play_time 播放时间
 * @property int $progress 进度
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserWatchTimeSegment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_watch_time_segment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id'], 'required'],
            [['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id', 'play_time', 'progress'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id'], 'unique', 'targetAttribute' => ['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id']],
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
            'play_time' => '播放时间',
            'progress' => '进度',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    public function beforeSave($insert) {
    	if(!parent::beforeSave($insert)) {
    		return false;
    	}
    	
    	$elementIds = array_column(CourseService::getSegment($this->segment_id) ?: [], 'id');
    	$list = UserWatchTimeElement::findAll($this->getAttributes(['user_id', 'periods_id', 'course_id', 'textbook_id', 'segment_id']));
    	
    	$this->play_time = 0;
    	$completes = 0;
    	foreach($list as $row) {
    		if(!in_array($row->element_id, $elementIds)) {
    			$row->delete();
    			continue;
    		}
    		if($row->max_play_time >= $row->duration) {
    			$completes ++;
    		}
    		if($row->is_playable) {
    			$this->play_time += $row->play_time;
    		}
    	}
    	$this->progress = floor(($completes / count($elementIds)) * 100);
    	
    	return true;
    }
    
    public function afterSave($insert, $changedAttributes) {
    	parent::afterSave($insert, $changedAttributes);
    	
    	$attrs = $this->getAttributes(['user_id', 'periods_id', 'course_id', 'textbook_id']);
    	$model = UserWatchTimeTextbook::findOne($attrs);
    	if(!$model) {
    		$model = new UserWatchTimeTextbook();
    		$model->attributes = $attrs;
    	}
    	$model->save(false);
    }
}
