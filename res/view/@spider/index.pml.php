<!DOCTYPE HTML>
<html lang="zh">
<!-- 此页面为蜘蛛用页面，不会包含CSS,JS元素 -->
<head>
    <?php Env::include("page_meta") -> render(); ?>
    <title><?php Env::echo(isset($title) ? $title : 'Three - 三人行，必有我师焉') ?></title>
</head>

<body>
    <header>
        <nav>
            <a href="/html/index.asp">Home</a>
            <a href="/html/html_intro.asp">Previous</a>
            <a href="/html/html_elements.asp">Next</a>
        </nav>
    </header>
    <main>
        <?php Env::markdown("## here is a markdown text\n```内容啊哈哈哈哈哈哈```") ?>

    </main>
    <footer>
    </footer>
</body>

</html>