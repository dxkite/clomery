<!DOCTYPE HTML>
<html>
<head>
	<title><?php Env::echo(isset($title) ? $title : "") ?></title>
	<?php Env::include("page_meta") -> render(); ?>
	<link rel="stylesheet" href="<?php echo Page::url('resource',['path'=>'main.css']) ?>">
</head>
<body>
	<header>
		<div id="header-top" class="clearfix">
			<div id="site-logo">

			</div>
			<div id="user-info">

			</div>
		</div>
		<nav id="nav-menu" class="clearfix" >
		<?php foreach($head_index as $index): ?>
			<a title="<?php Env::echo(isset($index['title']) ? $index['title'] : "") ?>" href="<?php Env::echo(isset($index['url']) ? $index['url'] : "") ?>"><div class="nav-menu-item <?php if(isset($index['select']) && $index['select']=true): ?> current <?php endif; ?>"> <?php Env::echo(isset($index['text']) ? $index['text'] : "") ?></div> </a>
		<?php endforeach; ?>
		</nav>
	</header>