-- create:2016-11-27 13:26:09

CREATE TABLE `site_setting` (
	`sid` int(11) NOT NULL  AUTO_INCREMENT COMMENT '设置ID',
	`name` varchar(80) NOT NULL   COMMENT '设置KEY',
	`type` varchar(10) NOT NULL   COMMENT '数据类型',
	`value` varchar(255) NOT NULL   COMMENT '设置数据',
	PRIMARY KEY (`sid`),
	UNIQUE KEY `name` (`name`),
	KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `upload` (
	`fid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '文件ID',
	`type` varchar(10) NOT NULL   COMMENT '扩展名',
	`hash` varchar(32) NOT NULL   COMMENT 'MD5哈希',
	`ref` int(11) NOT NULL   COMMENT '引用计数',
	PRIMARY KEY (`fid`),
	KEY `type` (`type`),
	KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `upload_usage` (
	`rid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '文件ID',
	`fid` bigint(20) NOT NULL   COMMENT '文件资源',
	`uid` bigint(20) NOT NULL   COMMENT '使用用户',
	`name` varchar(80) NOT NULL   COMMENT '文件名',
	`type` varchar(10) NOT NULL   COMMENT '扩展名',
	`time` int(11) NOT NULL   COMMENT '时间',
	`publish` int(1) NOT NULL DEFAULT '1'  COMMENT '是否公开',
	PRIMARY KEY (`rid`),
	KEY `fid` (`fid`),
	KEY `uid` (`uid`),
	KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user_option_log` (
	`oid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '日志ID',
	`uid` bigint(20) NOT NULL   COMMENT '使用的用户',
	`name` varchar(80) NOT NULL   COMMENT '操作名',
	`sketch` varchar(255) NOT NULL   COMMENT '操作附加描述',
	`ip` varchar(32) NOT NULL   COMMENT '使用令牌的ID',
	`time` int(11) NOT NULL   COMMENT '使用的时间',
	PRIMARY KEY (`oid`),
	KEY `uid` (`uid`),
	KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user_permision` (
	`gid` int(11) NOT NULL  AUTO_INCREMENT COMMENT '分组ID',
	`uid` bigint(20) NOT NULL   COMMENT '用户ID',
	`name` varchar(80) NOT NULL   COMMENT '分组名',
	`sort` int(11) NOT NULL   COMMENT '排序索引',
	`upload` enum('Y','N') NOT NULL DEFAULT 'N'  COMMENT '上传文件',
	PRIMARY KEY (`gid`),
	UNIQUE KEY `uid` (`uid`),
	KEY `name` (`name`),
	KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user_token` (
	`tid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '令牌ID',
	`uid` bigint(20) NOT NULL   COMMENT '使用的用户',
	`token` varchar(32) NOT NULL   COMMENT '令牌',
	`name` varchar(80) NOT NULL   COMMENT '命令名',
	`ip` varchar(32) NOT NULL   COMMENT '使用令牌的ID',
	`time` int(11) NOT NULL   COMMENT '使用的时间',
	`expire` int(11) NOT NULL   COMMENT '过期时间',
	`value` varchar(255) NOT NULL   COMMENT '附加值',
	PRIMARY KEY (`tid`),
	KEY `uid` (`uid`),
	KEY `token` (`token`),
	KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user` (
	`uid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '用户ID',
	`name` varchar(13) NOT NULL   COMMENT '用户名',
	`email` varchar(50) NOT NULL   COMMENT '邮箱',
	`password` varchar(60) NOT NULL   COMMENT '密码HASH',
	`groupid` bigint(20) NOT NULL DEFAULT '0'  COMMENT '分组ID',
	`verify_email` int(1) NOT NULL DEFAULT '0'  COMMENT '邮箱验证',
	PRIMARY KEY (`uid`),
	UNIQUE KEY `name` (`name`),
	UNIQUE KEY `email` (`email`),
	KEY `groupid` (`groupid`),
	KEY `verify_email` (`verify_email`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

