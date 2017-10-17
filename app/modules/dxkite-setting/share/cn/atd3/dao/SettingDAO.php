<?php
namespace cn\atd3\dao;

use suda\archive\Table;

class SettingDAO extends Table
{
    public function __construct()
    {
        parent::__construct('setting');
    }

    public function onBuildCreator($table) {
        return $table->fields(
            $table->field('id','bigint',20)->primary()->auto(),
            $table->field('name','varchar',255)->unique()->comment("设置名"),
            $table->field('value','text')->comment("设置的值")
        );
    }

    public function set(string $name, $value)
    {
        $get=$this->select('id', ['name'=>$name]);
        if ($get && ($val=$get->fetch())) {
            $id=$val['id'];
            $this->updateByPrimaryKey($id, ['value'=>serialize($value)]);
        } else {
            $id=$this->insertValue(null, $name, serialize($value));
        }
        return $id;
    }

    public function get(string $name)
    {
        $get=$this->select(['id','value'], ['name'=>$name]);
        if ($get && ($val=$get->fetch())) {
            return unserialize($val['value']);
        }
        return null;
    }
    
    public function remove(string $name)
    {
        $get=$this->select('id', ['name'=>$name]);
        if ($get && ($val=$get->fetch())) {
            $id=$val['id'];
            return $this->deleteByPrimaryKey($id);
        }
        return false;
    }
    
    public function list(int $page=null, int $rows=10)
    {
        if (is_null($page)) {
            $list=parent::list();
        } else {
            $list=parent::list($page, $rows);
        }
        if ($list) {
            array_walk($list, function (&$value, $key) {
                $value['value']=unserialize($value['value']);
            });
            return $list;
        }
        return null;
    }
}
