<?php if($_SQL->namespace): ?>namespace <?php template\Builder::echo($_SQL->namespace) ?>; 
<?php endif; ?>
class <?php template\Builder::echo($_SQL->name) ?> {
<?php foreach($_SQL->fields as $name => $field): ?>
    /** <?php template\Builder::echo($field) ?> */
    protected $<?php template\Builder::echo($name) ?>;
<?php endforeach; ?>
}