<?php
namespace archive;
use Storage;
use helper\Value;

// 创建储存对象
class Builder
{
    protected $fields;
    protected $sets;
    protected $auto;
    protected $namespace;
    protected $name;

    public function export(string $path){
        $_SQL=new Value(['fields'=>$this->$fields,'name'=>$name,'namespace'=>$namespace]);
    }

    public function load(string $path)
    {
        if (file_exists($path)) {
            $file=file($path);
            foreach ($file as $line) {
                if (preg_match('/^(?:\s*)(?!;)(\w+)\s+(\S+)(?:\s+(.+))?$/', $line, $match)) {
                    $this->fields[$match[1]]=$match[2];
                    $this->sets[$match[1]]=self::parser_str($match[3]);
                    if (isset($this->sets[$match[1]]['auto'])) {
                        $this->auto=$match[1];
                    }
                }
            }
        }
        $sql=self::getCreateSQL('user');
        Storage::mkdirs(SITE_TEMP);
        file_put_contents(SITE_TEMP.'/user.sql',$sql);
    }

    protected static function parser_str(string $sets)
    {
        $values=[];
        preg_match_all('/(\w+)(?:=(\'|")?(\S+)(?(2)\2))?\s*/', $sets, $matchs);
        for ($i=0;$i<count($matchs[0]);$i++) {
            $name=$matchs[1][$i];
            $str=strcmp($matchs[2][$i], '"') && strcmp($matchs[2][$i], '\'');
            $value=$matchs[3][$i];
            if (preg_match('/^(true|false)$/i', $matchs[3][$i])) {
                $value=$matchs[3][$i]==='true';
            } elseif (is_numeric($matchs[3][$i])) {
                settype($value, 'integer');
            }
            $values[$name]=$value;
        }
        return $values;
    }

    public function getCreateSQL(string $tablename):string
    {
        $create=[];
        $sets=[];
        foreach ($this->fields as $name => $type) {
            $auto=isset($this->sets[$name]['auto'])?'AUTO_INCREMENT':'';
            $null=isset($this->sets[$name]['null'])?'NULL':'NOT NULL';
            $comment=isset($this->sets[$name]['comment'])?('COMMENT \''.$this->sets[$name]['comment'].'\''):'';
            $default=isset($this->sets[$name]['default'])?'DEFAULT \''.addcslashes($this->sets[$name]['default'],'\'').'\'':'';
            $create[]=trim("`{$name}` {$type} {$null} {$default} {$auto} {$comment}");
            if (isset($this->sets[$name]['primary'])) {
                $sets[]="PRIMARY KEY (`{$name}`)";
            }
            elseif (isset($this->sets[$name]['unique'])) {
                $sets[]="UNIQUE KEY `{$name}` (`{$name}`)";
            }
            else if (isset($this->sets[$name]['key'])) {
                $sets[]="KEY `{$name}` (`{$name}`)";
            }
        }
        $sql="CREATE TABLE `{$tablename}` (\r\n\t";
        $sql.=implode(",\r\n\t",array_merge($create,$sets));
        $auto=$this->auto?'AUTO_INCREMENT=0':''; 
        $sql.="\r\n) ENGINE=InnoDB {$auto} DEFAULT CHARSET=utf8;";
        return $sql;
    }
    
    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return mixed
     */
    public function getSets()
    {
        return $this->sets;
    }

    /**
     * @return mixed
     */
    public function getAuto()
    {
        return $this->auto;
    }

    /**
     * @return string
     */
    public function getNamespace() : string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getName():string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

}
