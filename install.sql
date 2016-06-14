/*
Navicat MySQL Data Transfer

Source Server         : mysql_xampp
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : plants

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2014-07-11 11:03:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `end_admin`
-- ----------------------------
DROP TABLE IF EXISTS `end_admin`;
CREATE TABLE `end_admin` (
  `admin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rights_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `id` (`admin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of end_admin
-- ----------------------------
INSERT INTO `end_admin` VALUES ('1', '1', 'admin', '66be5f1f1b43bdb2e337c6749ac1228c0b9d1e24', '', null);


-- ----------------------------
-- Table structure for `end_category`
-- ----------------------------
DROP TABLE IF EXISTS `end_category`;
CREATE TABLE `end_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `update_time` int(11) unsigned NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `url` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `target` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'link',
  `page_title` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `system` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `item_count` int(11) NOT NULL,
  `image` varchar(200) COLLATE utf8_unicode_ci DEFAULT '',
  `short_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `category_id` (`category_id`),
  KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `end_config`
-- ----------------------------
DROP TABLE IF EXISTS `end_config`;
CREATE TABLE `end_config` (
  `config_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='admin settings';

-- ----------------------------
-- Records of end_config
-- ----------------------------
INSERT INTO `end_config` VALUES ('1', '0', 'site_name', '园丁园管理系统', '2014-07-11 10:57:32', 'text', '站点名字', '0');
INSERT INTO `end_config` VALUES ('18', '0', 'upload_file_types', '*.jpg;*.jpeg;*.gif;*.png;', '2013-06-03 08:34:39', 'text', '', '0');


-- ----------------------------
-- Table structure for `end_rights`
-- ----------------------------
DROP TABLE IF EXISTS `end_rights`;
CREATE TABLE `end_rights` (
  `rights_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `rights` text COLLATE utf8_unicode_ci,
  UNIQUE KEY `rights_id` (`rights_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of end_rights
-- ----------------------------
INSERT INTO `end_rights` VALUES ('1', '系统管理员', 'Can do everything', '9', 'category_view,category_add,category_update,category_delete,item_view,item_add,item_update,item_delete,account_update,admin_view,admin_add,admin_update,admin_update_password,admin_delete,config_view,config_add,config_update,config_delete,extension_view,extension_add,extension_update,extension_delete,upload_add,rights_view,rights_add,rights_update,rights_delete,activity_view,activity_delete,activity_update,activity_add,cankaoyusuan_view,cankaoyusuan_delete,cankaoyusuan_update,cankaoyusuan_add,canyurenshu_view,canyurenshu_delete,canyurenshu_update,canyurenshu_add,canyurenyuan_view,canyurenyuan_delete,canyurenyuan_update,canyurenyuan_add,cepingfangshi_view,cepingfangshi_delete,cepingfangshi_update,cepingfangshi_add,cepinggongju_view,cepinggongju_delete,cepinggongju_update,cepinggongju_add,changdiyaoqiu_view,changdiyaoqiu_delete,changdiyaoqiu_update,changdiyaoqiu_add,huodongleixing_view,huodongleixing_delete,huodongleixing_update,huodongleixing_add,huodongshijian_view,huodongshijian_delete,huodongshijian_update,huodongshijian_add,huodongxingshi_view,huodongxingshi_delete,huodongxingshi_update,huodongxingshi_add,shiyongfanwei_view,shiyongfanwei_delete,shiyongfanwei_update,shiyongfanwei_add,suzhipeiyangdian_view,suzhipeiyangdian_delete,suzhipeiyangdian_update,suzhipeiyangdian_add,teacher_view,teacher_delete,teacher_update,teacher_add,user_view,user_delete,user_update,user_add,user_right_view,user_right_delete,user_right_update,user_right_add,user_role_view,user_role_delete,user_role_update,user_role_add');
