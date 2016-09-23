<!DOCTYPE html>
<html>

<head>
    <title>404 - <?php Env::echo( $title ) ?></title>
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
    <div id="url"><?php Env::echo(isset($url) ? $url : '页面找不到了哦！') ?></div>
</body>

</html>