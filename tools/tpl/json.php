namespace api<? echo $namespace ?>;

use api\Visitor;
use api\Param;

<?php echo $comment;?>


class <?php echo ucfirst($name) ?> extends Visitor
{
    public $auth='<?php echo addslashes($permission) ?>';
    public $class=__CLASS__;

    public function apiMain(Param $param)
    {
       <?php echo $interface ?>
    }
}
