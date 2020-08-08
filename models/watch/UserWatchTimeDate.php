<?php

namespace app\models\watch;

use Yii;

/**
 * This is the model class for table "user_watch_time_date".
 *
 * @property int $user_id 用户ID
 * @property string $date 日期
 * @property int $play_time 播放时长
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserWatchTimeDate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_watch_time_date';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'date'], 'required'],
            [['user_id', 'play_time'], 'integer'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['user_id', 'date'], 'unique', 'targetAttribute' => ['user_id', 'date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'date' => '日期',
            'play_time' => '播放时长',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
