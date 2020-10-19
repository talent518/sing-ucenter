<?php

namespace app\models\account;

use Yii;

/**
 * This is the model class for table "teachers".
 *
 * @property int $id
 * @property int $type 教师类型：0:销售1部;1:新星妈妈;2:班主任;5:销售2部;6:销售3部;
 * @property int $squad 组ID
 * @property string $name 老师名称
 * @property string $qr 二维码
 * @property string $avatar 头像
 * @property string $media_id 微信素材ID
 * @property string $alias 老师微信号
 * @property string $invite_code 推荐码
 * @property int $status 状态：0、正常，1、禁用
 * @property int $class_status 带班状态：0、正常，1、禁用
 * @property int $user_id 绑定用户
 * @property int $cur_level 当前老师等级
 * @property int $threshold 阈值
 * @property int $threshold_max 最大阈值
 * @property int $is_del 是否删除
 * @property string $created_at
 * @property string $updated_at
 * @property string $we_work_uid 企业微信账号（user_id）
 */
class Teachers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_user');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'we_work_uid'], 'required'],
            [['type', 'squad', 'status', 'class_status', 'user_id', 'cur_level', 'threshold', 'threshold_max', 'is_del'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'alias'], 'string', 'max' => 30],
            [['qr', 'avatar', 'we_work_uid'], 'string', 'max' => 255],
            [['media_id'], 'string', 'max' => 50],
            [['invite_code'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'squad' => 'Squad',
            'name' => 'Name',
            'qr' => 'Qr',
            'avatar' => 'Avatar',
            'media_id' => 'Media ID',
            'alias' => 'Alias',
            'invite_code' => 'Invite Code',
            'status' => 'Status',
            'class_status' => 'Class Status',
            'user_id' => 'User ID',
            'cur_level' => 'Cur Level',
            'threshold' => 'Threshold',
            'threshold_max' => 'Threshold Max',
            'is_del' => 'Is Del',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'we_work_uid' => 'We Work Uid',
        ];
    }
}
