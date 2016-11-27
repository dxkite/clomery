-- create:2016-11-27 20:45:23

CREATE TABLE `article` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '文章ID',
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
	PRIMARY KEY (`id`),
	KEY `author` (`author`),
	KEY `categroy` (`categroy`),
	KEY `title` (`title`),
	KEY `verify` (`verify`),
	KEY `publish` (`publish`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `article_reply` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '回复ID',
	`aritcle` bigint(20) NOT NULL   COMMENT '回复的文章',
	`reply` bigint(20) NOT NULL   COMMENT '回复回复',
	`count` int(11) NOT NULL   COMMENT '被回复数',
	`author` bigint(20) NOT NULL   COMMENT '回复的人',
	`text` varchar(500) NOT NULL   COMMENT '回复内容',
	`time` int(11) NOT NULL   COMMENT '回复的时间',
	`ip` varchar(20) NOT NULL   COMMENT '回复IP',
	`state` tinyint(1) NOT NULL DEFAULT '1'  COMMENT '状态',
	PRIMARY KEY (`id`),
	KEY `aritcle` (`aritcle`),
	KEY `reply` (`reply`),
	KEY `count` (`count`),
	KEY `author` (`author`),
	KEY `state` (`state`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `article_tag` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '索引',
	`article_id` bigint(20) NOT NULL   COMMENT '文章ID',
	`tag_id` bigint(20) NOT NULL   COMMENT '标签ID',
	PRIMARY KEY (`id`),
	KEY `article_id` (`article_id`),
	KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `article_vote` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT 'ID',
	`article_id` bigint(20) NOT NULL   COMMENT '文章ID',
	`user_id` bigint(20) NOT NULL   COMMENT '用户ID',
	`score` int(1) NOT NULL   COMMENT '正赞负踩',
	`time` int(11) NOT NULL   COMMENT '操作时间',
	`ip` varchar(32) NOT NULL   COMMENT '操作IP',
	PRIMARY KEY (`id`),
	KEY `article_id` (`article_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `category` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '分类ID',
	`icon` bigint(20) NOT NULL   COMMENT '分类图标资源',
	`name` varchar(20) NOT NULL   COMMENT '分类名',
	`slug` varchar(20) NOT NULL   COMMENT '英文缩写',
	`discription` varchar(255) NOT NULL   COMMENT '分类描述',
	`sort` int(11) NOT NULL   COMMENT '排序',
	`count` int(11) NOT NULL   COMMENT '分类下的文章',
	`parent` bigint(20) NOT NULL   COMMENT '父分类',
	PRIMARY KEY (`id`),
	KEY `icon` (`icon`),
	KEY `name` (`name`),
	KEY `slug` (`slug`),
	KEY `sort` (`sort`),
	KEY `parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `notification_data` (
	`id` bigint(20) NOT NULL   COMMENT '通知ID',
	`data` text NOT NULL   COMMENT '通知数据',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `notification` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '通知ID',
	`send_id` bigint(20) NOT NULL   COMMENT '发送人',
	`recv_id` bigint(20) NOT NULL   COMMENT '接受人',
	`type` int(11) NOT NULL   COMMENT '通知类型',
	`time` int(11) NOT NULL   COMMENT '通知时间',
	`state` tinyint(1) NOT NULL   COMMENT '状态',
	`data_id` bigint(20) NOT NULL   COMMENT '通知内容',
	PRIMARY KEY (`id`),
	KEY `send_id` (`send_id`),
	KEY `recv_id` (`recv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `site_navigation` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '导航ID',
	`name` varchar(80) NOT NULL   COMMENT '导航名',
	`url` varchar(255) NOT NULL   COMMENT '导航URL',
	`sort` int(11) NOT NULL   COMMENT '排序',
	`parent` bigint(20) NOT NULL   COMMENT '父导航',
	PRIMARY KEY (`id`),
	KEY `name` (`name`),
	KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `site_setting` (
	`id` int(11) NOT NULL  AUTO_INCREMENT COMMENT '设置ID',
	`name` varchar(80) NOT NULL   COMMENT '设置KEY',
	`type` varchar(10) NOT NULL   COMMENT '数据类型',
	`value` varchar(255) NOT NULL   COMMENT '设置数据',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`),
	KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `tag` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '分类标签',
	`name` varchar(20) NOT NULL   COMMENT '标签名',
	`count` int(11) NOT NULL   COMMENT '标签下的内容',
	PRIMARY KEY (`id`),
	KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `upload` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '文件ID',
	`type` varchar(10) NOT NULL   COMMENT '扩展名',
	`hash` varchar(32) NOT NULL   COMMENT 'MD5哈希',
	`ref` int(11) NOT NULL   COMMENT '引用计数',
	PRIMARY KEY (`id`),
	KEY `type` (`type`),
	KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `upload_usage` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '文件ID',
	`upload_id` bigint(20) NOT NULL   COMMENT '文件资源',
	`uid` bigint(20) NOT NULL   COMMENT '使用用户',
	`name` varchar(80) NOT NULL   COMMENT '文件名',
	`type` varchar(10) NOT NULL   COMMENT '扩展名',
	`time` int(11) NOT NULL   COMMENT '时间',
	`publish` int(1) NOT NULL DEFAULT '1'  COMMENT '是否公开',
	PRIMARY KEY (`id`),
	KEY `upload_id` (`upload_id`),
	KEY `uid` (`uid`),
	KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user_group` (
	`id` int(11) NOT NULL  AUTO_INCREMENT COMMENT '分组ID',
	`user_id` bigint(20) NOT NULL   COMMENT '用户ID',
	`name` varchar(80) NOT NULL   COMMENT '分组名',
	`sort` int(11) NOT NULL   COMMENT '排序索引',
	`upload` enum('Y','N') NOT NULL DEFAULT 'N'  COMMENT '上传文件',
	PRIMARY KEY (`id`),
	UNIQUE KEY `user_id` (`user_id`),
	KEY `name` (`name`),
	KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user_option_log` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '日志ID',
	`user_id` bigint(20) NOT NULL   COMMENT '使用的用户',
	`name` varchar(80) NOT NULL   COMMENT '操作名',
	`sketch` varchar(255) NOT NULL   COMMENT '操作附加描述',
	`ip` varchar(32) NOT NULL   COMMENT '使用令牌的ID',
	`time` int(11) NOT NULL   COMMENT '使用的时间',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user_token` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '令牌ID',
	`user_id` bigint(20) NOT NULL   COMMENT '使用的用户',
	`token` varchar(32) NOT NULL   COMMENT '令牌',
	`name` varchar(80) NOT NULL   COMMENT '令牌名',
	`ip` varchar(32) NOT NULL   COMMENT '使用令牌的ID',
	`time` int(11) NOT NULL   COMMENT '使用的时间',
	`expire` int(11) NOT NULL   COMMENT '过期时间',
	`value` varchar(255) NOT NULL   COMMENT '附加值',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `token` (`token`),
	KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `user` (
	`id` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT '用户ID',
	`name` varchar(13) NOT NULL   COMMENT '用户名',
	`email` varchar(50) NOT NULL   COMMENT '邮箱',
	`password` varchar(60) NOT NULL   COMMENT '密码HASH',
	`group_id` bigint(20) NOT NULL DEFAULT '0'  COMMENT '分组ID',
	`verify_email` int(1) NOT NULL DEFAULT '0'  COMMENT '邮箱验证',
	`avatar` bigint(20) NOT NULL DEFAULT '0'  COMMENT '头像ID',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`),
	UNIQUE KEY `email` (`email`),
	KEY `group_id` (`group_id`),
	KEY `verify_email` (`verify_email`),
	KEY `avatar` (`avatar`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

