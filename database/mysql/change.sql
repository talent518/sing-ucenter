######## sql变更记录 #########
# 2020-02-04 zhoushan
ALTER TABLE `sing-order`.`user_order`
ADD COLUMN `coupon_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券金额（分）' AFTER `order_coupon_id`;

ALTER TABLE `sing-order`.`yunji_order`
ADD COLUMN `plan_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销售计划ID',
ADD COLUMN `plan_team_cate_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '进量计划ID';
ALTER TABLE `sing-order`.`yunji_order` ADD INDEX idx_plan_team_cate_id(`plan_team_cate_id`);

CREATE TABLE `sing-order`.`order_status_log` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`order_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '订单类型：1=内部订单；2=外部订单；3=兑换码订单',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联订单表主键ID',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_type_id` (`order_type`,`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单状态变更记录';


ALTER TABLE `sing-pub`.`plan_team_cate`
ADD COLUMN `real_num_out` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '外部订单实际进量' AFTER `real_num`,
ADD COLUMN `real_active_num_out` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '外部订单激活量' AFTER `real_active_num`;