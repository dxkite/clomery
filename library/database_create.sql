-- create:2016-11-26 12:04:13

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
	`name` varchar(80) NOT NULL   COMMENT '命令名',
	`ip` varchar(32) NOT NULL   COMMENT '使用令牌的ID',
	`time` int(11) NOT NULL   COMMENT '使用的时间',
	`expire` int(11) NOT NULL   COMMENT '过期时间',
	`value` varchar(255) NOT NULL   COMMENT '附加值',
	PRIMARY KEY (`tid`),
	KEY `uid` (`uid`),
	KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user` (
	`uid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '用户ID',
	`name` varchar(13) NOT NULL   COMMENT '用户名',
	`password` varchar(60) NOT NULL   COMMENT '密码HASH',
	`groupid` bigint(20) NOT NULL DEFAULT '0'  COMMENT '分组ID',
	PRIMARY KEY (`uid`),
	UNIQUE KEY `name` (`name`),
	KEY `groupid` (`groupid`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

