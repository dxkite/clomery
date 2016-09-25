<!DOCTYPE HTML>
<html lang="zh">
<!-- 此页面为蜘蛛用页面，不会包含CSS,JS元素 -->
<head>
    <?php Env::include("page_meta") -> render(); ?>
    <title><?php Env::echo(isset($title) ? $title : 'Three - 三人行，必有我师焉') ?></title>
</head>

<body>
    <header>
        <nav rel="index">
            <a href="<?php echo Page::url('home_page') ?>">Home</a>
            <a href="<?php echo Page::url('404_page') ?>">404Page</a>
        </nav>
    </header>
    <main>
        <?php Env::markdown($markdown_text) ?>

    </main>
    <footer>
    </footer>
</body>

</html>