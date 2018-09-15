/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : jdxcx

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-08-02 10:41:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jd_recovery
-- ----------------------------
DROP TABLE IF EXISTS `jd_recovery`;
CREATE TABLE `jd_recovery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '发布者ID号',
  `pics` varchar(700) NOT NULL DEFAULT '' COMMENT '图片',
  `des` text NOT NULL COMMENT '服务介绍',
  `view` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '服务状态；0：默认；1：审核通过；-1：审核不通过',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址；格式化',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '地点名称',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '地点详细描述',
  `latitude` double NOT NULL DEFAULT '0' COMMENT '纬度',
  `longitude` double NOT NULL DEFAULT '0' COMMENT '经度',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上次更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
