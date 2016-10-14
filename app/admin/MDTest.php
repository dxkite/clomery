<?php
$md = new Markdown_Manager;
$md->setUrlsave(['https://www.baidu.com/img/baidu_jgylogo3.gif']);
$md->uploadZipMarkdown(APP_RES.'/tmp/mix-xss.zip');
