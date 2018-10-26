/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50638
 Source Host           : localhost:3306
 Source Schema         : aizhua

 Target Server Type    : MySQL
 Target Server Version : 50638
 File Encoding         : 65001

 Date: 20/10/2018 17:58:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cmf_shop_account
-- ----------------------------
DROP TABLE IF EXISTS `cmf_shop_account`;
CREATE TABLE `cmf_shop_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` char(100) NOT NULL,
  `password` char(100) NOT NULL,
  `disabled` tinyint(1) unsigned DEFAULT '0',
  `ctime` int(10) unsigned DEFAULT '0',
  `token` char(100) DEFAULT NULL,
  `openid` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家账号登录信息表';

SET FOREIGN_KEY_CHECKS = 1;
