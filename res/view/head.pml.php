<!DOCTYPE HTML>
<html lang="<?php Env::echo($_Page->lang('zh')) ?>">
<head>
	<title><?php Env::echo($_Page->title($_L->main_title('芒刺中国'))) ?></title>
	<?php echo Page::insert('head_htmlhead') ?>
	<?php Env::include("page_meta") -> render(); ?>
	<link rel="stylesheet" href="<?php echo Page::url('resource',['path'=>'css/main.css']) ?>">
</head>
<body>
	<header>
		<div id="header-top" class="clearfix">
			<div id="site-logo">
			<?php if($_Op->site_logo): ?>
				<a href="http://<?php Env::echo($_SERVER['SERVER_NAME']) ?>"><img src="<?php Env::echo($_Op->site_logo) ?>" alt="site-logo" title="<?php Env::echo($_Op->site_name) ?>"></img></a>
			<?php else: ?>
				<div class="site-title"><?php Env::echo($_Op->site_name) ?></div>
			<?php endif; ?>
			</div>
			<div id="user-info">
				<?php if($_Page->has_signin): ?>
			 		<div><a href="<?php echo Page::url('user') ?>"><?php Env::echo($_Page->signin_user) ?></a>，你好！</div>
				<?php else: ?>
				<nav>
					<?php if( $_Op->allowSignUp == 1): ?>
					<a href="<?php echo Page::url('user',['path'=>'SignUp']) ?>"><?php Env::echo($_L->signup('注册')) ?></a>
					<?php endif; ?>
					<a href="<?php echo Page::url('user',['path'=>'SignIn']) ?>"><?php Env::echo($_L->signin('登陆')) ?></a>
				</nav>
				<?php endif; ?>
			</div>
		</div>
		<nav id="nav-menu" class="clearfix" >
		<?php foreach($_Page->head_index_nav as $at=>$index): ?>
			<a title="<?php Env::echo($index['title']) ?>" href="<?php Env::echo($index['url']) ?>"><div class="nav-menu-item <?php if( isset($_Page->head_index_nav_select) && $_Page->head_index_nav_select==$at): ?> current <?php endif; ?>"> <?php Env::echo($_L->_($index['text'])) ?></div> </a>
		<?php endforeach; ?>
		</nav>
	</header>