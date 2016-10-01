<!DOCTYPE HTML>
<html>
<head>
	<title><?php Env::echo(isset($site_title)?$site_title:Env::Options()->getSitename()) ?></title>
	<?php echo Page::insert('head_htmlhead') ?>
	<?php Env::include("page_meta") -> render(); ?>
	<link rel="stylesheet" href="<?php echo Page::url('resource',['path'=>'css/main.css']) ?>">
</head>
<body>
	<header>
		<div id="header-top" class="clearfix">
			<div id="site-logo">
				<a href="http://<?php Env::echo(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "") ?>"><img src="<?php echo Page::url('resource',['path'=>'img/mccn.gif']) ?>" alt="site-logo" title="<?php Env::echo(Env::Options()->getSitename()) ?>"></img></a>
			</div>
			<div id="user-info">

			</div>
		</div>
		<nav id="nav-menu" class="clearfix" >
		<?php foreach($head_index_nav as $at=>$index): ?>
			<a title="<?php Env::echo(isset($index['title']) ? $index['title'] : "") ?>" href="<?php Env::echo(isset($index['url']) ? $index['url'] : "") ?>"><div class="nav-menu-item <?php if($head_index_nav_select==$at): ?> current <?php endif; ?>"> <?php Env::echo(isset($index['text']) ? $index['text'] : "") ?></div> </a>
		<?php endforeach; ?>
		</nav>
	</header>