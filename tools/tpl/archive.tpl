
<?php if($_SQL->namespace): ?>namespace <?php template\Builder::echo($_SQL->namespace) ?>; 
<?php endif; ?>

use archive\Archive;
use archive\Condition;
use archive\Statement;

class <?php template\Builder::echo($_SQL->name) ?> implements Archive {
    protected static $_fields=<?php template\Builder::echo($this->getFieldsStr()) ?>;
<?php foreach($_SQL->fields as $name => $field): ?>
<?php  $comment =isset($this->sets[$name]['comment'])? $this->sets[$name]['comment'] :$field; ?>
<?php  $type = preg_match('/int/i',$field)?'int':'string'; ?>
    /**
     * <?php template\Builder::echo($comment) ?> 
     * @var  <?php template\Builder::echo($type) ?> 
     */
    protected $<?php template\Builder::echo($name) ?>;
<?php endforeach; ?>

<?php foreach($_SQL->fields as $name => $field): ?>
<?php  $type = preg_match('/int/i',$field)?'int':'string'; ?>


    /**
     * @return  <?php template\Builder::echo($_SQL->name) ?>   
     */
    public function set<?php template\Builder::echo(ucfirst($name)) ?>(<?php template\Builder::echo($type) ?> $<?php template\Builder::echo($name) ?>) {
        $this-><?php template\Builder::echo($name) ?>=$<?php template\Builder::echo($name) ?>;
        return $this;
    }

    /**
     * @return  <?php template\Builder::echo($type) ?>   
     */
    public function get<?php template\Builder::echo(ucfirst($name)) ?>() : <?php template\Builder::echo($type) ?> {
        return $this-><?php template\Builder::echo($name) ?>;
    }
<?php endforeach; ?>
    function getFeilds():array
    {
        return self::$_fields;
    }
    function getAvailableFields():array
    {
        $available=[];
        foreach (self::$_fields as $name){
            if (isset($this->{$name})){
                $available[]=$name;
            }
        }
        return $available;
    }
    function tableCreator():string{
        return '<?php template\Builder::echo(addslashes($this->getCreateSQL())) ?>';
    }
    function sqlCreate():Statement{
		$values=self::getAvailableFields();
		$param=[];
		$bind='';
		$names='';
		foreach ($values as $name)
		{
			$bind.=':'.$name.',';
			$names.='`'.$name.'`,';
			$param[$name]=$this->{$name};
		}
		$sql='INSERT INTO `<?php template\Builder::echo($this->getTableName()) ?>` ('.trim($names,',').') VALUES ('.trim($bind,',').');';
		return new Statement($sql,$param);
    }
    function sqlRetrieve(Condition $condition):Statement{
		
	}
    function sqlUpdate():Statement{}
    function sqlDelete():Statement{}
}