-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-10-06 10:28:31
-- 服务器版本： 10.1.10-MariaDB
-- PHP Version: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mongci`
--

-- --------------------------------------------------------

--
-- 表的结构 `atd_bugs`
--

CREATE TABLE `atd_bugs` (
  `id` int(11) NOT NULL,
  `user` varchar(80) NOT NULL,
  `discription` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_bugs`
--

INSERT INTO `atd_bugs` (`id`, `user`, `discription`, `time`, `status`) VALUES
(1, 'SixerMe', '用户名可使用空格', '2016-10-05 13:18:36', 1),
(2, '_KaQqi', '特殊字符 UNICODE控制字符->RLO 导致出错', '2016-10-05 13:54:20', 0);

-- --------------------------------------------------------

--
-- 表的结构 `atd_nav`
--

CREATE TABLE `atd_nav` (
  `id` int(11) NOT NULL,
  `name` varchar(24) NOT NULL,
  `url` tinytext NOT NULL,
  `title` varchar(255) NOT NULL,
  `show` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_nav`
--

INSERT INTO `atd_nav` (`id`, `name`, `url`, `title`, `show`, `sort`, `parent`) VALUES
(1, 'index', '/', '', 1, 1, 0),
(2, 'notes', '/notes', '', 1, 6, 0),
(3, 'article', '/article', '', 1, 2, 0),
(4, 'books', '/books', '', 1, 3, 0),
(5, 'question', '/question', '', 1, 4, 0),
(7, 'test', '/test', 'OnlineJudge', 1, 5, 0),
(9, 'about', '/about', '', 1, 7, 0);

-- --------------------------------------------------------

--
-- 表的结构 `atd_signin_historys`
--

CREATE TABLE `atd_signin_historys` (
  `hid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_signin_historys`
--

INSERT INTO `atd_signin_historys` (`hid`, `uid`, `ip`, `time`) VALUES
(1, 43, '127.0.0.1', 1475722631),
(2, 45, '127.0.0.1', 1475722691),
(3, 46, '127.0.0.1', 1475722991),
(4, 47, '127.0.0.1', 1475723206),
(5, 43, '127.0.0.1', 1475725148),
(6, 43, '127.0.0.1', 1475725860),
(7, 43, '127.0.0.1', 1475725895),
(8, 43, '127.0.0.1', 1475728023);

-- --------------------------------------------------------

--
-- 表的结构 `atd_site_options`
--

CREATE TABLE `atd_site_options` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站设置表';

--
-- 转存表中的数据 `atd_site_options`
--

INSERT INTO `atd_site_options` (`id`, `name`, `value`) VALUES
(1, 'site_name', '芒刺中国'),
(2, 'theme', 'default'),
(19, 'site_logo', '/static/img/mccn.svg'),
(20, 'keywords', '芒刺,程序员,文摘'),
(21, 'lang', 'zh_cn'),
(22, 'HV_SignUp', '0'),
(23, 'HV_SignIn', '0'),
(24, 'HV_Post', '0'),
(25, 'HV_Comment', '0'),
(26, 'allowSignUp', '1'),
(27, 'copyright', '芒刺中国');

-- --------------------------------------------------------

--
-- 表的结构 `atd_users`
--

CREATE TABLE `atd_users` (
  `uid` bigint(20) NOT NULL,
  `uname` varchar(12) NOT NULL,
  `upass` varchar(60) NOT NULL,
  `gid` int(11) NOT NULL DEFAULT '2',
  `signup` int(11) NOT NULL,
  `signin` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `email_verify` enum('Y','N') NOT NULL DEFAULT 'N',
  `lastip` varchar(32) NOT NULL,
  `token` varchar(32) NOT NULL COMMENT '登陆验证值',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_users`
--

INSERT INTO `atd_users` (`uid`, `uname`, `upass`, `gid`, `signup`, `signin`, `email`, `email_verify`, `lastip`, `token`, `status`) VALUES
(47, 'dddd', '$2y$10$wBPNnCrRl9272PSeMZyjOOo5x81XAB/RmOk9neWb7V1xd7Pz/bfBu', 2, 1475723206, 0, 'dddd@q.c', 'N', '127.0.0.1', 'd2749033b0c840f2a799389dfb1567ee', 0),
(46, 'ddddd', '$2y$10$GvYQQAlFjC175csViVoS9eGkZ9JdxTTrDdnTZ6DzPQkxQB8K5.RR6', 2, 1475722991, 0, 'd@d.c', 'N', '127.0.0.1', 'ef9ec8193f2a12635b81dabec5aa5edc', 0),
(44, 'admin', '$2y$10$e0R6lH7pS2Og6DhRqpcaZ.ex.5WWZM0yGieCg/Y9CJT4pJMNCRAcW', 2, 1475669954, 0, 'admin@atd3.cn', 'N', '127.0.0.1', '4f992374c5dc8e7ec07e514539f47ea3', 0),
(45, 'dxkite', '$2y$10$H75Hoqn5cA/aHfcOCsyAJeJQ1Fkn14n2sw/FCixhVk5JOA4Fbeoga', 2, 1475722691, 0, 'dxkite@2.c', 'N', '127.0.0.1', '9aa62bf7746fbc777a8b85ee3c64516e', 0),
(43, 'hello', '$2y$10$Ld4pc3sUM3lT4fr1UPwQt.VlTcQkBA0kB/1eHdH.1ReDiFwjwO9bu', 2, 1475652025, 1475728023, 'helloworld@atd3.cn', 'N', '127.0.0.1', 'da93e83667e2efcc9fd93881282ad687', 0);

-- --------------------------------------------------------

--
-- 表的结构 `atd_user_group`
--

CREATE TABLE `atd_user_group` (
  `gid` int(11) NOT NULL,
  `gname` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表';

--
-- 转存表中的数据 `atd_user_group`
--

INSERT INTO `atd_user_group` (`gid`, `gname`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- 表的结构 `atd_user_group_relationship`
--

CREATE TABLE `atd_user_group_relationship` (
  `uid` bigint(20) NOT NULL,
  `gid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atd_bugs`
--
ALTER TABLE `atd_bugs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `atd_nav`
--
ALTER TABLE `atd_nav`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `parent` (`parent`);

--
-- Indexes for table `atd_signin_historys`
--
ALTER TABLE `atd_signin_historys`
  ADD PRIMARY KEY (`hid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `atd_site_options`
--
ALTER TABLE `atd_site_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_2` (`name`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `atd_users`
--
ALTER TABLE `atd_users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD UNIQUE KEY `uname` (`uname`),
  ADD KEY `uid_2` (`uid`),
  ADD KEY `uid_3` (`uid`),
  ADD KEY `uid_4` (`uid`);

--
-- Indexes for table `atd_user_group`
--
ALTER TABLE `atd_user_group`
  ADD PRIMARY KEY (`gid`),
  ADD KEY `gname` (`gname`);

--
-- Indexes for table `atd_user_group_relationship`
--
ALTER TABLE `atd_user_group_relationship`
  ADD KEY `uid` (`uid`),
  ADD KEY `gid` (`gid`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `atd_bugs`
--
ALTER TABLE `atd_bugs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `atd_nav`
--
ALTER TABLE `atd_nav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- 使用表AUTO_INCREMENT `atd_signin_historys`
--
ALTER TABLE `atd_signin_historys`
  MODIFY `hid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- 使用表AUTO_INCREMENT `atd_site_options`
--
ALTER TABLE `atd_site_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- 使用表AUTO_INCREMENT `atd_users`
--
ALTER TABLE `atd_users`
  MODIFY `uid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- 使用表AUTO_INCREMENT `atd_user_group`
--
ALTER TABLE `atd_user_group`
  MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
