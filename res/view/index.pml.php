<?php Env::include("head") -> render(); ?>
<?php Page::insertCallback('hello',function () { ?>
<h1>hello</h1>
<?php });?>
<?php Env::include("developing") -> render(); ?>
<?php Env::include("footer") -> render(); ?>