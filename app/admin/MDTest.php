<?php
$md = new Markdown_Manager;
$md->setUrlsave(['https://www.baidu.com/img/baidu_jgylogo3.gif']);
var_dump($md->uploadZipMarkdown(APP_RES.'/tmp/mix-xss.zip'));
var_dump(ArticleManager::numbers());
var_dump(ArticleManager::getArticlesList());
