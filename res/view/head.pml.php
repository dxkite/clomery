<!DOCTYPE HTML>
<html>
<head>
	<title><?php Env::echo(isset($title) ? $title : 'Three - 三人行，必有我师焉') ?></title>
	<?php Env::include("page_meta") -> render(); ?>
	<link rel="stylesheet" href="<?php echo Page::url('resource',['path'=>'main.css']) ?>">
</head>