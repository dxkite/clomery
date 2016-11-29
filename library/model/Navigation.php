<?php

namespace model;

use Query;

class Navigation
{
    const STATE_SHOW=1;
    const STATE_HIDDEN=2;

    public function create(string $name, string $url, int $sort=0, int $parent=0)
    {
        return Query::insert('site_navigation', ['name'=>$name, 'url'=>$url, 'sort'=>$sort, 'parent'=>$parent, 'state'=>self::STATE_SHOW]);
    }
    
    public function delete(int $id)
    {
        return Query::delete('site_navigation', ['id'=>$id]);
    }

    public function update(int $id, string $name, string $url, int $sort, int $parent)
    {
        return Query::update('site_navigation', ['id'=>$id, 'name'=>$name, 'url'=>$url, 'sort'=>$sort, 'parent'=>$parent]);
    }
    
    public function list()
    {
        return Query::where('site_navigation', ['id', 'name', 'url', 'state', 'sort', 'parent'], '`state`=:state ORDER BY `sort` ASC', ['state'=>self::STATE_SHOW])->fetchAll();
    }

    public function listAll()
    {
        return Query::where('site_navigation', ['id', 'name', 'url', 'state', 'sort', 'parent'])->fetchAll();
    }
}
