<?php Env::include("head") -> render(); ?>
<body>
	<nav>
	<?php foreach($head_index as $index): ?>
		<div><a title="<?php Env::echo(isset($index['title']) ? $index['title'] : "[?]") ?>" href="<?php Env::echo(isset($index['url']) ? $index['url'] : "[?]") ?>"> <?php Env::echo(isset($index['text']) ? $index['text'] : "[?]") ?> </a></div>
	<?php endforeach; ?>
	</nav>
</body>
</html>