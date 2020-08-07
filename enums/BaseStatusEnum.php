<?php
/**
 * Created by PhpStorm.
 * User: 张冰
 * Date: 2019/9/10
 * Time: 15:30
 */

namespace app\enums;


class BaseStatusEnum extends Enum
{
    const 启用 = 1;
    const 禁用 = 2;

    //订单状态
    const ORDER_STATUS_ING = 0; //待付款
    const ORDER_STATUS_SUCCESS = 1; //付款成功
    const ORDER_STATUS_FAILED = 2; //付款失败
    const ORDER_STATUS_REFUND = 3; //退款成功
    const ORDER_STATUS_GROUP_SUCCESS = 4; //拼团成功
    const ORDER_STATUS_REFUND_PART = 5; //部分退款

    //订单支付类型：1-微信 2-支付宝 3-云集，4-京东，100、其它
    const ORDER_TYPE_WECHAT = 1; //微信
    const ORDER_TYPE_ALIPAY = 2; //支付宝
    const ORDER_TYPE_ELSE = 3; //其他
    const ORDER_TYPE_JD = 4; //京东
    const ORDER_TYPE_EDUXXX = 5;//平台

    //内部订单购买方式
    const ORDER_BUY_TYPE_SINGLE = 1; //单买
    const ORDER_BUY_TYPE_GROUP = 2; //团购
    const ORDER_BUY_TYPE_CODE = 3; //兑换码

    //订单状态修改日志的订单类型
    const ORDER_STATUS_LOG_TYPE_INSIDE = 1;//内部订单
    const ORDER_STATUS_LOG_TYPE_OUTSIDE = 2;//外部订单


    //默认活动方案
    const PERIODS_CLASS_SOURCE_SYSTEM = 2; //2、系统订单随机分配

}