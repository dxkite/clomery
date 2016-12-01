<? echo $comment ?>



return api_permision('<?php echo addslashes($permission) ?>', function ( $param) {
    <?php echo $interface ?>
});

