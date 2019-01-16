<?php
namespace dxkite\category\controller;

use dxkite\category\table\CategoryTable;
use suda\tool\Command;

class CategoryController
{
    protected $table;
    protected $target;

    public function __construct(string $target = null)
    {
        $this->target = Command::newClassInstance($target);
        $this->table = new CategoryTable($this->target);
    }


    public function ids2name(array $ids)
    {
        $get = $this->table->select(['id','name'], ['id'=>$ids])->fetchAll();
        if ($get) {
            $result=[];
            foreach ($get as $item) {
                $result[$item['id']]=$item['name'];
            }
            return $result;
        }
        return false;
    }

    public function getCategoryByIds(array $ids):?array
    {
        $get = $this->table->select(['id','name','slug'], ['id'=>$ids])->fetchAll();
        if ($get) {
            $result=[];
            foreach ($get as $item) {
                $result[$item['id']]=$item;
            }
            return $result;
        }
        return null;
    }

    public function name2id(string $name)
    {
        return $this->table->select($this->getFields(), ' LOWER(name)=LOWER(:name) ', ['name'=>$name])->fetch()['id']??false;
    }

    public function slug2id(string $slug)
    {
        return $this->table->select($this->getFields(), ' LOWER(slug)=LOWER(:slug) ', ['slug'=>$name])->fetch()['id']??false;
    }
    
    public function setCategory(int $target, int $category)
    {
        $get = $this->target->getByPrimaryKey($target);
        if ($get) {
            if ($get['category']==$category) {
                return true;
            }
            try {
                $this->table->begin();
                $article->setCategory($target, $category);
                $this->countCate($category);
                $this->countCate($get['category'], false);
                $this->table->commit();
                return true;
            } catch (\PDOException $e) {
                $this->table->rollBack();
                return false;
            }
        }
        return false;
    }

    protected function countCate(int $cateid, bool $op=true)
    {
        if ($op) {
            return $this->table->update('count = count +1', ['id'=>$cateid]);
        } else {
            return  $this->table->update('count = count -1', ['id'=>$cateid]);
        }
    }

    public function add(int $uid, string $name, string $slug, int $parent=0)
    {
        return $this->table->insert([
            'name'=>$name,
            'slug'=>$slug,
            'parent'=>$parent,
            'user'=>$uid,
        ]);
    }
}
