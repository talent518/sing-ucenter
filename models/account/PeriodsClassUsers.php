<?php

namespace app\models\account;

use Yii;

/**
 * This is the model class for table "periods_class_users".
 *
 * @property int $id
 * @property int $goods_id 商品ID（冗余）
 * @property int $periods_id 期数ID
 * @property int $class_id 班级ID
 * @property int $sys_order_id 系统订单ID
 * @property int $channel_order_id 外部订单ID
 * @property int $user_id 用户ID
 * @property int $is_view_course 是否可看课程
 * @property int $is_buy 是否为购课用户
 * @property int $is_add_teacher 0、暂未处理，1、老师主动添加用户，2、用户主动添加老师，3、待通过，4、手机号不是微信号，5、用户已拒绝
 * @property string $desc 备注
 * @property int|null $weight 意向等级
 * @property int $is_del 是否删除
 * @property string $created_at
 * @property string $updated_at
 * @property int $code_id 渠道ID
 * @property int $activity_base_id 活动基础表ID
 * @property int $valid_idcard 风控身份验证
 * @property int $dis_type 进班分配类型（0：系统分配，1：指定CC）
 * @property string|null $add_time_hj 通过时间
 * @property int $is_add_teacher_wx
 * @property int|null $add_time 企业微信添加时间
 * @property int $is_get_guide 是否领取攻略 0否 1 是
 * @property int $is_break_ice 是否有有效沟通  0否 1 是
 * @property int $channel_level 渠道等级快照
 * @property int $is_true_break_ice 是否破冰（开课前领取小程序以及有有效沟通）
 * @property int $is_del_teacher 企业微信是否删除老师 0 否 1是
 * @property string $special_marking 分班特殊标记，多个以逗号分割（0=无，1=一线城市）
 */
class PeriodsClassUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'periods_class_users';
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
            [['goods_id', 'periods_id', 'class_id', 'sys_order_id', 'channel_order_id', 'user_id', 'is_view_course', 'is_buy', 'is_add_teacher', 'weight', 'is_del', 'code_id', 'activity_base_id', 'valid_idcard', 'dis_type', 'is_add_teacher_wx', 'add_time', 'is_get_guide', 'is_break_ice', 'channel_level', 'is_true_break_ice', 'is_del_teacher'], 'integer'],
            [['desc'], 'required'],
            [['desc'], 'string'],
            [['created_at', 'updated_at', 'add_time_hj'], 'safe'],
            [['special_marking'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'Goods ID',
            'periods_id' => 'Periods ID',
            'class_id' => 'Class ID',
            'sys_order_id' => 'Sys Order ID',
            'channel_order_id' => 'Channel Order ID',
            'user_id' => 'User ID',
            'is_view_course' => 'Is View Course',
            'is_buy' => 'Is Buy',
            'is_add_teacher' => 'Is Add Teacher',
            'desc' => 'Desc',
            'weight' => 'Weight',
            'is_del' => 'Is Del',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'code_id' => 'Code ID',
            'activity_base_id' => 'Activity Base ID',
            'valid_idcard' => 'Valid Idcard',
            'dis_type' => 'Dis Type',
            'add_time_hj' => 'Add Time Hj',
            'is_add_teacher_wx' => 'Is Add Teacher Wx',
            'add_time' => 'Add Time',
            'is_get_guide' => 'Is Get Guide',
            'is_break_ice' => 'Is Break Ice',
            'channel_level' => 'Channel Level',
            'is_true_break_ice' => 'Is True Break Ice',
            'is_del_teacher' => 'Is Del Teacher',
            'special_marking' => 'Special Marking',
        ];
    }
}
