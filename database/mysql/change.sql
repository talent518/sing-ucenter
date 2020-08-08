/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 5.7.11 : Database - sing-ucenter
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`sing-ucenter` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `sing-ucenter`;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'sso用户ID',
  `token` varchar(100) NOT NULL DEFAULT '' COMMENT 'token',
  `token_expire` bigint(18) unsigned NOT NULL DEFAULT '0' COMMENT 'token过期时间',
  `sso_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'sso token',
  `user_info` json NOT NULL COMMENT '用户信息',
  `created_at` bigint(18) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` bigint(18) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `user_integral` */

DROP TABLE IF EXISTS `user_integral`;

CREATE TABLE `user_integral` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `stars` int(10) NOT NULL DEFAULT '0' COMMENT '素材时长',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户星星数'
/*!50100 PARTITION BY HASH (user_id)
PARTITIONS 128 */;

/*Table structure for table `user_integral_log` */

DROP TABLE IF EXISTS `user_integral_log`;

CREATE TABLE `user_integral_log` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `periods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '期数ID',
  `course_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `dest_type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '目标类型(1教材,2环节,3学习报告,4调查问卷,5生成证书,6分享证书,7礼品兑换)',
  `dest_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '目标ID',
  `flag` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '标示',
  `stars` int(10) NOT NULL DEFAULT '0' COMMENT '星星数',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`,`periods_id`,`course_id`,`dest_type`,`dest_id`,`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户星星明细'
/*!50100 PARTITION BY HASH (user_id)
PARTITIONS 128 */;

/*Table structure for table `user_watch_time` */

DROP TABLE IF EXISTS `user_watch_time`;

CREATE TABLE `user_watch_time` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `play_time` int(10) NOT NULL DEFAULT '0' COMMENT '播放时长',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户看课总时长'
/*!50100 PARTITION BY HASH (user_id)
PARTITIONS 128 */;

/*Table structure for table `user_watch_time_date` */

DROP TABLE IF EXISTS `user_watch_time_date`;

CREATE TABLE `user_watch_time_date` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `date` date NOT NULL DEFAULT '0000-00-00' COMMENT '日期',
  `play_time` int(10) NOT NULL DEFAULT '0' COMMENT '播放时长',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`user_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户每天看课时长'
/*!50100 PARTITION BY HASH (user_id)
PARTITIONS 128 */;

/*Table structure for table `user_watch_time_element` */

DROP TABLE IF EXISTS `user_watch_time_element`;

CREATE TABLE `user_watch_time_element` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `periods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '期数ID',
  `course_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `textbook_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教材ID',
  `segment_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '环节ID',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '素材ID',
  `duration` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '素材时长',
  `play_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '播放时间',
  `is_playable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否可播放',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`user_id`,`periods_id`,`course_id`,`textbook_id`,`segment_id`,`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户看课时长明细'
/*!50100 PARTITION BY HASH (user_id)
PARTITIONS 128 */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
