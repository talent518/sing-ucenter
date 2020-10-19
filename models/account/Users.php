<?php

namespace app\models\account;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $user_id 用户id
 * @property string $union_id 公众平台唯一ID
 * @property string $nickname 昵称
 * @property string $avatar 头像
 * @property string $mobile 手机号（可缺省）
 * @property string|null $birthday 宝宝生日
 * @property int $age_range 宝宝年龄段，0:未知，1:0~3, 2:4+
 * @property int $sex 宝宝性别：0、保密，1、男，2、女
 * @property string $baby_name 宝宝昵称
 * @property int $level 宝宝级别
 * @property string|null $desc 用户信息
 * @property string $relation_mobile 关联手机号
 * @property string $wx_number 微信号
 * @property string $last_login_at
 * @property int $is_del 是否删除
 * @property string $created_at
 * @property string $updated_at
 * @property string $baby_image 宝宝头像
 * @property string $channel 用户注册来源渠道code
 * @property int $is_experience 宝宝是否有英语经验; 0无，1有；默认无经验
 * @property string $external_userid 企业微信外部用户id
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
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
            [['birthday', 'last_login_at', 'created_at', 'updated_at'], 'safe'],
            [['age_range', 'sex', 'level', 'is_del', 'is_experience'], 'integer'],
            [['desc'], 'string'],
            [['union_id'], 'string', 'max' => 35],
            [['nickname', 'baby_name', 'external_userid'], 'string', 'max' => 50],
            [['avatar', 'baby_image'], 'string', 'max' => 200],
            [['mobile', 'relation_mobile', 'wx_number'], 'string', 'max' => 15],
            [['channel'], 'string', 'max' => 30],
            [['union_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'union_id' => 'Union ID',
            'nickname' => 'Nickname',
            'avatar' => 'Avatar',
            'mobile' => 'Mobile',
            'birthday' => 'Birthday',
            'age_range' => 'Age Range',
            'sex' => 'Sex',
            'baby_name' => 'Baby Name',
            'level' => 'Level',
            'desc' => 'Desc',
            'relation_mobile' => 'Relation Mobile',
            'wx_number' => 'Wx Number',
            'last_login_at' => 'Last Login At',
            'is_del' => 'Is Del',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'baby_image' => 'Baby Image',
            'channel' => 'Channel',
            'is_experience' => 'Is Experience',
            'external_userid' => 'External Userid',
        ];
    }
}
