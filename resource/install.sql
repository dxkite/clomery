-- ----------------------------------------------------------
-- PHP Simple Library XCore 2.0.0 Database Backup File
-- Create On 2016-11-28 21:34:15
-- Host: localhost   Database: 
-- Server version	10.1.10-MariaDB
-- ------------------------------------------------------
/*!40101 SET NAMES utf8 */;

--
-- Create Table article
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `author` bigint(20) NOT NULL COMMENT '作者',
  `categroy` int(11) NOT NULL COMMENT '文章分类',
  `title` varchar(255) NOT NULL COMMENT '文章标题',
  `abstract` varchar(255) NOT NULL COMMENT '摘要',
  `content` text NOT NULL COMMENT '文章内容',
  `type` tinyint(1) NOT NULL COMMENT '内容类型',
  `view` int(11) NOT NULL COMMENT '阅读',
  `create` int(11) NOT NULL COMMENT '创建时间',
  `update` int(11) NOT NULL COMMENT '最后更新',
  `replys` int(11) NOT NULL COMMENT '回复',
  `allow_reply` tinyint(1) NOT NULL DEFAULT '1' COMMENT '可回复',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '文章状态',
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `categroy` (`categroy`),
  KEY `title` (`title`),
  KEY `state` (`state`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;




INSERT INTO `article` (`id`,`author`,`categroy`,`title`,`abstract`,`content`,`type`,`view`,`create`,`update`,`replys`,`allow_reply`,`state`) VALUES ('1','1','1','Hello','Abstract','Content','0','0','1480339311','1480339311','0','1','4'),('2','1','1','Hello','Abstract','Content','0','0','1480339312','1480339312','0','1','4'),('3','1','2','Hello','Abstract','Content','0','0','1480339380','1480339380','0','1','4');


--
-- Create Table article_reply
--

DROP TABLE IF EXISTS `article_reply`;
CREATE TABLE `article_reply` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '回复ID',
  `aritcle` bigint(20) NOT NULL COMMENT '回复的文章',
  `reply` bigint(20) NOT NULL COMMENT '回复回复',
  `count` int(11) NOT NULL COMMENT '被回复数',
  `author` bigint(20) NOT NULL COMMENT '回复的人',
  `text` varchar(500) NOT NULL COMMENT '回复内容',
  `time` int(11) NOT NULL COMMENT '回复的时间',
  `ip` varchar(20) NOT NULL COMMENT '回复IP',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `aritcle` (`aritcle`),
  KEY `reply` (`reply`),
  KEY `count` (`count`),
  KEY `author` (`author`),
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `article_reply` (`id`,`aritcle`,`reply`,`count`,`author`,`text`,`time`,`ip`,`state`) VALUES ;


--
-- Create Table article_tag
--

DROP TABLE IF EXISTS `article_tag`;
CREATE TABLE `article_tag` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '索引',
  `article` bigint(20) NOT NULL COMMENT '文章ID',
  `tag` bigint(20) NOT NULL COMMENT '标签ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  KEY `article` (`article`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;




INSERT INTO `article_tag` (`id`,`article`,`tag`) VALUES ('1','3','1'),('2','3','2'),('3','3','3');


--
-- Create Table categroy
--

DROP TABLE IF EXISTS `categroy`;
CREATE TABLE `categroy` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `icon` bigint(20) NOT NULL COMMENT '分类图标资源',
  `name` varchar(20) NOT NULL COMMENT '分类名',
  `slug` varchar(20) NOT NULL COMMENT '英文缩写',
  `discription` varchar(255) NOT NULL COMMENT '分类描述',
  `sort` int(11) NOT NULL COMMENT '排序',
  `count` int(11) NOT NULL COMMENT '分类下的文章',
  `parent` bigint(20) NOT NULL COMMENT '父分类',
  PRIMARY KEY (`id`),
  KEY `icon` (`icon`),
  KEY `name` (`name`),
  KEY `slug` (`slug`),
  KEY `sort` (`sort`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;




INSERT INTO `categroy` (`id`,`icon`,`name`,`slug`,`discription`,`sort`,`count`,`parent`) VALUES ('1','1','Test','test','ddddd','0','1','0');


--
-- Create Table notification
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '通知ID',
  `send` bigint(20) NOT NULL COMMENT '发送人',
  `recv` bigint(20) NOT NULL COMMENT '接受人',
  `type` int(11) NOT NULL COMMENT '通知类型',
  `time` int(11) NOT NULL COMMENT '通知时间',
  `state` tinyint(1) NOT NULL COMMENT '状态',
  `data` bigint(20) NOT NULL COMMENT '通知内容',
  PRIMARY KEY (`id`),
  KEY `send` (`send`),
  KEY `recv` (`recv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `notification` (`id`,`send`,`recv`,`type`,`time`,`state`,`data`) VALUES ;


--
-- Create Table notification_data
--

DROP TABLE IF EXISTS `notification_data`;
CREATE TABLE `notification_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '通知ID',
  `data` text NOT NULL COMMENT '通知数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `notification_data` (`id`,`data`) VALUES ;


--
-- Create Table site_navigation
--

DROP TABLE IF EXISTS `site_navigation`;
CREATE TABLE `site_navigation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '导航ID',
  `name` varchar(80) NOT NULL COMMENT '导航名',
  `url` varchar(255) NOT NULL COMMENT '导航URL',
  `sort` int(11) NOT NULL COMMENT '排序',
  `parent` bigint(20) NOT NULL COMMENT '父导航',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `site_navigation` (`id`,`name`,`url`,`sort`,`parent`) VALUES ;


--
-- Create Table site_setting
--

DROP TABLE IF EXISTS `site_setting`;
CREATE TABLE `site_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `name` varchar(80) NOT NULL COMMENT '设置KEY',
  `type` varchar(10) NOT NULL COMMENT '数据类型',
  `value` varchar(255) NOT NULL COMMENT '设置数据',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `site_setting` (`id`,`name`,`type`,`value`) VALUES ;


--
-- Create Table tag
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '分类标签',
  `name` varchar(20) NOT NULL COMMENT '标签名',
  `count` int(11) NOT NULL COMMENT '标签下的内容',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;




INSERT INTO `tag` (`id`,`name`,`count`) VALUES ('1','0','1'),('2','0','1'),('3','EHELE','1');


--
-- Create Table token
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '令牌ID',
  `user` bigint(20) NOT NULL COMMENT '使用的用户',
  `token` varchar(32) NOT NULL COMMENT '令牌',
  `client` bigint(20) NOT NULL COMMENT '客户端',
  `ip` varchar(32) NOT NULL COMMENT '使用令牌的ID',
  `time` int(11) NOT NULL COMMENT '使用的时间',
  `expire` int(11) NOT NULL COMMENT '过期时间',
  `value` varchar(255) NOT NULL COMMENT '附加值',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `token` (`token`),
  KEY `client` (`client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `token` (`id`,`user`,`token`,`client`,`ip`,`time`,`expire`,`value`) VALUES ;


--
-- Create Table token_client
--

DROP TABLE IF EXISTS `token_client`;
CREATE TABLE `token_client` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '客户端ID',
  `name` varchar(80) NOT NULL COMMENT '客户端名',
  `description` varchar(255) NOT NULL COMMENT '客户端描述',
  `token` varchar(32) NOT NULL COMMENT '客户端识别码',
  `time` int(11) NOT NULL COMMENT '创建时间',
  `state` int(1) NOT NULL COMMENT '客户端状态',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `token` (`token`),
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `token_client` (`id`,`name`,`description`,`token`,`time`,`state`) VALUES ;


--
-- Create Table upload
--

DROP TABLE IF EXISTS `upload`;
CREATE TABLE `upload` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `type` varchar(10) NOT NULL COMMENT '扩展名',
  `hash` varchar(32) NOT NULL COMMENT 'MD5哈希',
  `ref` int(11) NOT NULL COMMENT '引用计数',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `upload` (`id`,`type`,`hash`,`ref`) VALUES ;


--
-- Create Table upload_usage
--

DROP TABLE IF EXISTS `upload_usage`;
CREATE TABLE `upload_usage` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `upload_id` bigint(20) NOT NULL COMMENT '文件资源',
  `uid` bigint(20) NOT NULL COMMENT '使用用户',
  `name` varchar(80) NOT NULL COMMENT '文件名',
  `type` varchar(10) NOT NULL COMMENT '扩展名',
  `time` int(11) NOT NULL COMMENT '时间',
  `publish` int(1) NOT NULL DEFAULT '1' COMMENT '是否公开',
  PRIMARY KEY (`id`),
  KEY `upload_id` (`upload_id`),
  KEY `uid` (`uid`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `upload_usage` (`id`,`upload_id`,`uid`,`name`,`type`,`time`,`publish`) VALUES ;


--
-- Create Table user
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `name` varchar(13) NOT NULL COMMENT '用户名',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `password` varchar(60) NOT NULL COMMENT '密码HASH',
  `group_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '分组ID',
  `verify_email` int(1) NOT NULL DEFAULT '0' COMMENT '邮箱验证',
  `avatar` bigint(20) NOT NULL DEFAULT '0' COMMENT '头像ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`),
  KEY `group_id` (`group_id`),
  KEY `verify_email` (`verify_email`),
  KEY `avatar` (`avatar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `user` (`id`,`name`,`email`,`password`,`group_id`,`verify_email`,`avatar`) VALUES ;


--
-- Create Table user_group
--

DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `name` varchar(80) NOT NULL COMMENT '分组名',
  `sort` int(11) NOT NULL COMMENT '排序索引',
  `upload` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '上传文件',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `name` (`name`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `user_group` (`id`,`user_id`,`name`,`sort`,`upload`) VALUES ;


--
-- Create Table user_option_log
--

DROP TABLE IF EXISTS `user_option_log`;
CREATE TABLE `user_option_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `user_id` bigint(20) NOT NULL COMMENT '使用的用户',
  `name` varchar(80) NOT NULL COMMENT '操作名',
  `sketch` varchar(255) NOT NULL COMMENT '操作附加描述',
  `ip` varchar(32) NOT NULL COMMENT '使用令牌的ID',
  `time` int(11) NOT NULL COMMENT '使用的时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `user_option_log` (`id`,`user_id`,`name`,`sketch`,`ip`,`time`) VALUES ;


--
-- Create Table vote_reply
--

DROP TABLE IF EXISTS `vote_reply`;
CREATE TABLE `vote_reply` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `article_id` bigint(20) NOT NULL COMMENT '文章ID',
  `reply_id` bigint(20) NOT NULL COMMENT '回复ID',
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `score` int(1) NOT NULL COMMENT '正赞负踩',
  `time` int(11) NOT NULL COMMENT '操作时间',
  `ip` varchar(32) NOT NULL COMMENT '操作IP',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `reply_id` (`reply_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




INSERT INTO `vote_reply` (`id`,`article_id`,`reply_id`,`user_id`,`score`,`time`,`ip`) VALUES ;


