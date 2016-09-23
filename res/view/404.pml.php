<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 - <?php Env::echo(isset($title) ? $title : '页面找不到了哦！') ?></title>
    <style>
        #text {
            font-size: 18em;
            text-align: center;
            width: 100%;
        }
        
        #url {
            font-size: 2em;
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="text">404</div>
    <?php if(isset($url)): ?>
    <div id="url">网页：<?php Env::echo( $url) ?> 找不到相关元素。</div>
    <?php else: ?>
    <div id="url">（*/∇＼*）页面找不到了啦！</div>
    <?php endif; ?>
</body>

</html>