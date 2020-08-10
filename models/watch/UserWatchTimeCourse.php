<?php

namespace app\models\watch;

use Yii;
use app\services\watch\CourseService;

/**
 * This is the model class for table "user_watch_time_course".
 *
 * @property int $user_id 用户ID
 * @property int $periods_id 期数ID
 * @property int $course_id 课程ID
 * @property int $play_time 播放时间
 * @property int $progress 进度
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserWatchTimeCourse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_watch_time_course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'periods_id', 'course_id'], 'required'],
            [['user_id', 'periods_id', 'course_id', 'play_time', 'progress'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'periods_id', 'course_id'], 'unique', 'targetAttribute' => ['user_id', 'periods_id', 'course_id']],
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
    	
    	$textbookIds = (CourseService::getCourse($this->course_id) ?: []);
    	$list = UserWatchTimeTextbook::findAll($this->getAttributes(['user_id', 'periods_id', 'course_id']));
    	
    	$this->play_time = 0;
    	$completes = 0;
    	foreach($list as $row) {
    		if(!in_array($row->textbook_id, $textbookIds)) {
    			$row->delete();
    			continue;
    		}
    		if($row->progress == 100) {
    			$completes ++;
    		}
    		$this->play_time += $row->play_time;
    	}
    	$this->progress = floor(($completes / count($textbookIds)) * 100);
    	
    	return true;
    }
}