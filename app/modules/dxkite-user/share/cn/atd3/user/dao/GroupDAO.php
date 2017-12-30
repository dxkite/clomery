<?php
namespace cn\atd3\user\dao;

use suda\archive\Table;
use suda\core\Query;
use suda\core\Application;
use suda\core\Storage;
use suda\core\Config;
use suda\tool\Json;
use cn\atd3\visitor\Permission;

/**
 * TODO:
 *  - Filter Auths
 */
class GroupDAO extends Table
{
    const INVAILD_NAME=-1;
    public function __construct()
    {
        parent::__construct(conf('module.tables.group', 'user_group'));
    }

    public function onBuildCreator($table) {
        return $table->fields(
            $table->field('id','bigint',20)->primary()->unsigned()->auto(),
            $table->field('name','varchar',255)->unique()->comment("分组名"),
            $table->field('permissions','text')->comment("权限控制表"),
            $table->field('sort','int',11)->key()->comment("排序索引")
        );
    }
    
    public function add(string $name, array $permissons, int $sort=0)
    {
        if ($this->select('id', ['name'=>$name])->fetch()>0) {
            return GroupDAO::INVAILD_NAME;
        }
        $id= $this->insert([
            'name'=>$name,
            'permissions'=> serialize(new Permission($permissons)),
            'sort'=>$sort,
        ]);
        return $id;
    }
    
    public function edit(int $id, string $name, array $permissons, int $sort=0)
    {
        $id= $this->updateByPrimaryKey($id, [
            'name'=>$name,
            'permissions'=> serialize(new Permission($permissons)),
            'sort'=>$sort,
        ]);
        return $id;
    }

    public function setPermission(int $id, array $permissons)
    {
        return $this->updateByPrimaryKey($id, [
            'permissions'=> serialize(new Permission($permissons)),
        ]);
    }
    
    public function getPermission(int $id)
    {
        $permissons=$this->setFields(['permissions'])->getByPrimaryKey($id);
        if ($permissons) {
            return unserialize($permissons['permissions']);
        }
        return false;
    }

    public function getById(int $id)
    {
        $permissons=$this->setFields(['name','permissions'])->getByPrimaryKey($id);
        if ($permissons) {
            $permissons['permissions']=unserialize($permissons['permissions']);
            return $permissons;
        }
        return false;
    }
    
    public function filter(array $array)
    {
        $that=$this;
        $list=array_keys($that->getAuthList());
        return array_filter($array, function ($value) use ($list) {
            return in_array($value, $list);
        });
    }

    public function list(int $page=null, int $rows=10)
    {
        $this->setWants(['id','name','permissions']);
        if (is_null($page)) {
            $list=parent::list();
        } else {
            $list=parent::list($page, $rows);
        }
        if ($list) {
            array_walk($list, function (&$value, $key) {
                $value['permissions']=unserialize($value['permissions']);
            });
            return $list;
        }
        return null;
    }

    public function groups2name(array $id)
    {
        $gid=$this->select(['id','name'], ['id'=>$id]);
        if ($gid) {
            return $gid->fetchAll();
        }
        return null;
    }
    
    public function checkPermission(int $id, $needs)
    {
        $needs=is_array($needs)?$needs:[$needs];
        $permissons=self::getPermission($id);
        return $permissons->surpass(new Permission($needs));
    }
}
