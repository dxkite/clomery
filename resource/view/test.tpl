<html>
    <head>
        <title><?php template\Builder::echo($_Page->title('Test Title')) ?></title>
    </head>
    <?php Event::pop('Page:test')->exec() ?>;
    <body>
        <?php template\Builder::echo($_Page->content('No Content')) ?>
    </body>
</html>