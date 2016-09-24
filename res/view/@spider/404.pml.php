<!DOCTYPE html>
<html lang="zh">

<head>
    <?php Env::include("page_meta") -> render(); ?>
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
    <div id="url"> URL: <?php Env::echo(isset( $url) ?  $url : "[NULL]") ?> 找不到相关元素。</div>
    <?php else: ?>
    <div id="url">（*/∇＼*）页面找不到了啦！</div>
    <?php endif; ?>
</body>

</html>