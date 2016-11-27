-- create:2016-11-27 20:28:29

CREATE TABLE `article` (
	`aid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '文章ID',
	`author` bigint(20) NOT NULL   COMMENT '作者',
	`categroy` int(11) NOT NULL   COMMENT '文章分类',
	`title` varchar(255) NOT NULL   COMMENT '文章标题',
	`abstract` varchar(255) NOT NULL   COMMENT '摘要',
	`content` text NOT NULL   COMMENT '文章内容',
	`ctype` tinyint(1) NOT NULL   COMMENT '内容类型',
	`view` int(11) NOT NULL   COMMENT '阅读',
	`create` int(11) NOT NULL   COMMENT '创建时间',
	`update` int(11) NOT NULL   COMMENT '最后更新',
	`replys` int(11) NOT NULL   COMMENT '回复',
	`allow_reply` tinyint(1) NOT NULL DEFAULT '1'  COMMENT '可回复',
	`verify` tinyint(1) NOT NULL DEFAULT '0'  COMMENT '文章审核',
	`publish` tinyint(1) NOT NULL DEFAULT '1'  COMMENT '发布',
	PRIMARY KEY (`aid`),
	KEY `author` (`author`),
	KEY `categroy` (`categroy`),
	KEY `title` (`title`),
	KEY `verify` (`verify`),
	KEY `publish` (`publish`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `article_reply` (
	`reply_id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '回复ID',
	`aritcle` bigint(20) NOT NULL   COMMENT '回复的文章',
	`reply` bigint(20) NOT NULL   COMMENT '回复回复',
	`count` int(11) NOT NULL   COMMENT '被回复数',
	`author` bigint(20) NOT NULL   COMMENT '回复的人',
	`text` varchar(500) NOT NULL   COMMENT '回复内容',
	`time` int(11) NOT NULL   COMMENT '回复的时间',
	`ip` varchar(20) NOT NULL   COMMENT '回复IP',
	`state` tinyint(1) NOT NULL DEFAULT '1'  COMMENT '状态',
	PRIMARY KEY (`reply_id`),
	KEY `aritcle` (`aritcle`),
	KEY `reply` (`reply`),
	KEY `count` (`count`),
	KEY `author` (`author`),
	KEY `state` (`state`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `article_tag` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '索引',
	`aid` bigint(20) NOT NULL   COMMENT '文章ID',
	`tag_id` bigint(20) NOT NULL   COMMENT '标签ID',
	PRIMARY KEY (`id`),
	KEY `aid` (`aid`),
	KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `article_vote` (
	`vote_id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT 'ID',
	`aid` bigint(20) NOT NULL   COMMENT '文章ID',
	`uid` bigint(20) NOT NULL   COMMENT '用户ID',
	`score` int(1) NOT NULL   COMMENT '正赞负踩',
	`time` int(11) NOT NULL   COMMENT '操作时间',
	`ip` varchar(32) NOT NULL   COMMENT '操作IP',
	PRIMARY KEY (`vote_id`),
	KEY `aid` (`aid`),
	KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `category` (
	`category_id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '分类ID',
	`icon` bigint(20) NOT NULL   COMMENT '分类图标资源',
	`name` varchar(20) NOT NULL   COMMENT '分类名',
	`slug` varchar(20) NOT NULL   COMMENT '英文缩写',
	`discription` varchar(255) NOT NULL   COMMENT '分类描述',
	`sort` int(11) NOT NULL   COMMENT '排序',
	`count` int(11) NOT NULL   COMMENT '分类下的文章',
	`parent` bigint(20) NOT NULL   COMMENT '父分类',
	PRIMARY KEY (`category_id`),
	KEY `icon` (`icon`),
	KEY `name` (`name`),
	KEY `slug` (`slug`),
	KEY `sort` (`sort`),
	KEY `parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `notification_data` (
	`nid` bigint(20) NOT NULL   COMMENT '通知ID',
	`data` text NOT NULL   COMMENT '通知数据',
	PRIMARY KEY (`nid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `notification` (
	`nid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '通知ID',
	`send_id` bigint(20) NOT NULL   COMMENT '发送人',
	`recv_id` bigint(20) NOT NULL   COMMENT '接受人',
	`type` int(11) NOT NULL   COMMENT '通知类型',
	`time` int(11) NOT NULL   COMMENT '通知时间',
	`state` tinyint(1) NOT NULL   COMMENT '状态',
	`date` bigint(20) NOT NULL   COMMENT '通知内容',
	PRIMARY KEY (`nid`),
	KEY `send_id` (`send_id`),
	KEY `recv_id` (`recv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `site_navigation` (
	`nav_id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '导航ID',
	`name` varchar(80) NOT NULL   COMMENT '导航名',
	`url` varchar(255) NOT NULL   COMMENT '导航URL',
	`sort` int(11) NOT NULL   COMMENT '排序',
	`parent` bigint(20) NOT NULL   COMMENT '父导航',
	PRIMARY KEY (`nav_id`),
	KEY `name` (`name`),
	KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `site_setting` (
	`set_id` int(11) NOT NULL  AUTO_INCREMENT COMMENT '设置ID',
	`name` varchar(80) NOT NULL   COMMENT '设置KEY',
	`type` varchar(10) NOT NULL   COMMENT '数据类型',
	`value` varchar(255) NOT NULL   COMMENT '设置数据',
	PRIMARY KEY (`set_id`),
	UNIQUE KEY `name` (`name`),
	KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `tag` (
	`tag_id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '分类标签',
	`name` varchar(20) NOT NULL   COMMENT '标签名',
	`count` int(11) NOT NULL   COMMENT '标签下的内容',
	PRIMARY KEY (`tag_id`),
	KEY `name` (`name`)
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
	`log_id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '日志ID',
	`uid` bigint(20) NOT NULL   COMMENT '使用的用户',
	`name` varchar(80) NOT NULL   COMMENT '操作名',
	`sketch` varchar(255) NOT NULL   COMMENT '操作附加描述',
	`ip` varchar(32) NOT NULL   COMMENT '使用令牌的ID',
	`time` int(11) NOT NULL   COMMENT '使用的时间',
	PRIMARY KEY (`log_id`),
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
	`name` varchar(80) NOT NULL   COMMENT '令牌名',
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
	`avatar` bigint(20) NOT NULL DEFAULT '0'  COMMENT '头像ID',
	PRIMARY KEY (`uid`),
	UNIQUE KEY `name` (`name`),
	UNIQUE KEY `email` (`email`),
	KEY `groupid` (`groupid`),
	KEY `verify_email` (`verify_email`),
	KEY `avatar` (`avatar`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

