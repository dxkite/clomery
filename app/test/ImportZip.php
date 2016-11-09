<?php

// 添加本地的zip文章到数据库
$md = new Blog_MdManager;
$md->setUrlsave(['https://www.baidu.com/img/baidu_jgylogo3.gif']);
var_dump($md->uploadZipMarkdown(APP_RES.'/tmp/mix-xss.zip'));
var_dump(Blog_Article::countPublic());