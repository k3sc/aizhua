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

 Date: 13/10/2018 19:28:31
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cmf_clamp_config
-- ----------------------------
DROP TABLE IF EXISTS `cmf_clamp_config`;
CREATE TABLE `cmf_clamp_config` (
  `index` int(10) unsigned DEFAULT NULL,
  `start` int(10) unsigned DEFAULT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  `count` int(10) unsigned DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
