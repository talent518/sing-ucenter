<?php

namespace app\models\watch;

use Yii;

/**
 * This is the model class for table "user_watch_time".
 *
 * @property int $user_id 用户ID
 * @property int $play_time 播放时长
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class UserWatchTime extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_watch_time';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'play_time'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'play_time' => '播放时长',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
