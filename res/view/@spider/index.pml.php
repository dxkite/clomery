<!DOCTYPE HTML>
<?php /*  此页面为蜘蛛用页面，不会包含CSS,JS元素  */ ?>
<html>
<head>
    <mata name="author" content="<?php Env::echo(isset($page['author']) ? $page['author'] : 'atd3.cn') ?>" >
    <meta name="discription" content="atd3.cn page; <?php Env::echo(isset( $page['discription'] ) ?  $page['discription']  : "[NULL]") ?> ">
    <meta name="keywords" content=" atd3.cn,<?php Env::echo(isset( $page['keywords'] ) ?  $page['keywords']  : "[NULL]") ?>">
    <meta name="revised" content="<?php Env::echo(isset($page['author']) ? $page['author'] : 'atd3.cn') ?>">
    <meta name="generator" content="Pomelo Template Compiler">
    <mata name="other" content="this page use for spider,without css or js,is native html document!"></mate>
	<title><?php Env::echo(isset($title) ? $title : 'Three - 三人行，必有我师焉') ?></title>
</head>
<body>
	<p class="text-center">Spider View</p>
</body>
</html>