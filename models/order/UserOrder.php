<?php

namespace app\models\order;

use Yii;

/**
 * This is the model class for table "user_order".
 *
 * @property int $id
 * @property string $out_trade_no 交易订单号
 * @property string $transcation_id 微信订单号
 * @property int $user_id 用户ID
 * @property int $goods_id 商品ID
 * @property int $periods_id 期数ID
 * @property int $user_address_id 收货地址
 * @property int $money 实付金额（分）
 * @property int $buy_type 购买方式：1、单买，2、团购
 * @property int $order_group_id 团ID
 * @property int $order_coupon_id 优惠券ID（优惠券商品则是创建的优惠券ID，其它商品则是使用的优惠券ID）
 * @property int $coupon_money 优惠券金额（分）
 * @property string $invite_type 推广人类型
 * @property int $invite_id 推广人ID
 * @property int $invite_earnings 推广人可得的收益
 * @property int $teacher_id 推广老师id
 * @property int $is_captain 是否为团长
 * @property string|null $callback 回调
 * @property string $desc 备注
 * @property int $status 订单状态：0、待付款，1、付款成功，2、付款失败，3、退款成功，4、拼团成功，5、部分退款
 * @property int|null $order_type 支付类型 1-微信 2-支付宝 3-云集，4-京东，100、其它
 * @property int $order_tag 订单标签：0、体验课，1、引流课，2、转化课，3、转化后体验课
 * @property string $pay_at 支付时间
 * @property string $created_at
 * @property string $updated_at
 * @property int $class_source 活动基础表ID（activity_base_id.id）
 */
class UserOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_order';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_order');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'goods_id', 'periods_id', 'user_address_id', 'money', 'buy_type', 'order_group_id', 'order_coupon_id', 'coupon_money', 'invite_id', 'invite_earnings', 'teacher_id', 'is_captain', 'status', 'order_type', 'order_tag', 'class_source'], 'integer'],
            [['callback'], 'string'],
            [['pay_at', 'created_at', 'updated_at'], 'safe'],
            [['out_trade_no'], 'string', 'max' => 64],
            [['transcation_id'], 'string', 'max' => 32],
            [['invite_type'], 'string', 'max' => 40],
            [['desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'out_trade_no' => 'Out Trade No',
            'transcation_id' => 'Transcation ID',
            'user_id' => 'User ID',
            'goods_id' => 'Goods ID',
            'periods_id' => 'Periods ID',
            'user_address_id' => 'User Address ID',
            'money' => 'Money',
            'buy_type' => 'Buy Type',
            'order_group_id' => 'Order Group ID',
            'order_coupon_id' => 'Order Coupon ID',
            'coupon_money' => 'Coupon Money',
            'invite_type' => 'Invite Type',
            'invite_id' => 'Invite ID',
            'invite_earnings' => 'Invite Earnings',
            'teacher_id' => 'Teacher ID',
            'is_captain' => 'Is Captain',
            'callback' => 'Callback',
            'desc' => 'Desc',
            'status' => 'Status',
            'order_type' => 'Order Type',
            'order_tag' => 'Order Tag',
            'pay_at' => 'Pay At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'class_source' => 'Class Source',
        ];
    }
}
