<?php if($_SQL->namespace): ?>namespace <?php template\Builder::echo($_SQL->namespace) ?>; 
<?php endif; ?>
class <?php template\Builder::echo($_SQL->name) ?> {
<?php foreach($_SQL->fields as $name => $field): ?>
    /** <?php template\Builder::echo($field) ?> */
    protected $<?php template\Builder::echo($name) ?>;
<?php endforeach; ?>

<?php foreach($_SQL->fields as $name => $field): ?>
<?php  $type = preg_match('/int/i',$field)?'int':'string'; ?>
    public function set<?php template\Builder::echo(ucfirst($name)) ?>(<?php template\Builder::echo($type) ?> $<?php template\Builder::echo($name) ?>){
        $this-><?php template\Builder::echo($name) ?>=$<?php template\Builder::echo($name) ?>;
        return $this;
    }
<?php endforeach; ?>
}