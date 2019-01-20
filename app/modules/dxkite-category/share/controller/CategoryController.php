<?php
namespace dxkite\category\controller;

use suda\tool\Command;
use dxkite\support\view\PageData;
use dxkite\support\view\TablePager;
use dxkite\category\table\CategoryTable;

class CategoryController
{
    /**
     * 分类表
     *
     * @var CategoryTable
     */
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

    public function getList(?int $page=null, int $count=10):PageData
    {
        return TablePager::listWhere($this->table, '1', [], $page, $count);
    }

    public function getByName(string $name):?array
    {
        return $this->table->select($this->table->getFields(), ' LOWER(name)=LOWER(:name) ', ['name'=>$name])->fetch();
    }

    public function getBySlug(string $slug):?array
    {
        return $this->table->select($this->table->getFields(), ' LOWER(slug)=LOWER(:slug) ', ['slug'=>$slug])->fetch();
    }
    
    public function count(?int $articleId, int $category)
    {
        if ($articleId === null) {
            $this->countCate($category);
        } else {
            $article = $this->target->getByPrimaryKey($articleId);
            if (\is_array($article)) {
                if ($category !== $article['category']) {
                    $this->countCate($category);
                    $this->countCate($article['category'], false);
                }
            }
        }
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
            'count'=> 0,
            'parent'=>$parent,
            'user'=>$uid,
        ]);
    }
}
