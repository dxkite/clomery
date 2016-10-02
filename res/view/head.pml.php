<!DOCTYPE HTML>
<html>
<head>
	<title><?php Env::echo($site_title) ?></title>
	<?php echo Page::insert('head_htmlhead') ?>
	<?php Env::include("page_meta") -> render(); ?>
	<link rel="stylesheet" href="<?php echo Page::url('resource',['path'=>'css/main.css']) ?>">
</head>
<body>
	<header>
		<div id="header-top" class="clearfix">
			<div id="site-logo">
			<?php if($__Op->site_logo): ?>
				<a href="http://<?php Env::echo($_SERVER['SERVER_NAME']) ?>"><img src="<?php Env::echo($__Op->site_logo) ?>" alt="site-logo" title="<?php Env::echo($__Op->site_title) ?>"></img></a>
			<?php else: ?>
				<div class="site-title"><?php Env::echo($__Op->site_title) ?></div>
			<?php endif; ?>
			</div>
			<div id="user-info">
				<nav>
					<a href="<?php echo Page::url('user',['path'=>'SignUp']) ?>">注册</a>
					<a href="<?php echo Page::url('user',['path'=>'SignIn']) ?>">登陆</a>
				</nav>
			</div>
		</div>
		<nav id="nav-menu" class="clearfix" >
		<?php foreach($head_index_nav as $at=>$index): ?>
			<a title="<?php Env::echo($index['title']) ?>" href="<?php Env::echo($index['url']) ?>"><div class="nav-menu-item <?php if( isset($head_index_nav_select) && $head_index_nav_select==$at): ?> current <?php endif; ?>"> <?php Env::echo($index['text']) ?></div> </a>
		<?php endforeach; ?>
		</nav>
	</header>