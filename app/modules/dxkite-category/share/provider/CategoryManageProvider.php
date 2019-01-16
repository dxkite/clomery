<?php
namespace dxkite\category\provider;

use dxkite\category\controller\CategoryController;
use dxkite\support\view\TablePager;
use suda\tool\Command;
use dxkite\category\table\CategoryTable;

class CategoryManageProvider
{
    protected $target;
    protected $table;

    public function __construct(string $target = null)
    {
        $this->target = Command::newClassInstance($target);
        $this->table = new CategoryTable($this->target);
    }

    public function add(string $name, string $slug, int $parent=0)
    {
        $uid = get_user_id();
        return $this->table->insert([
            'name'=>$name,
            'slug'=>$slug,
            'parent'=>$parent,
            'user'=>$uid,
        ]);
    }

    public function edit(int $id, array $edit)
    {
        return $this->table->updateByPrimaryKey($id, $edit);
    }

    public function delete(int $id)
    {
        return $this->table->deleteByPrimaryKey($id);
    }

    public function get(int $id=0)
    {
        return $this->table->getByPrimaryKey($id);
    }

    public function list(?int $page=null, int $row=10)
    {
        $pager = TablePager::listWhere($this->table, '1', [], $page, $row);
        return $pager;
    }
}
