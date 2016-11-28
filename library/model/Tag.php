<?php

namespace model;

use Query;

class Tag
{
    public function create(string $name)
    {
        try {
            return Query::insert('tag', ['name'=>$name]);
        } catch (\Exception $e) {
            return self::getId($name);
        }
        return 0;
    }
    
    public function delete(int $id)
    {
        return Query::delete('tag', ['id'=>$id]);
    }

    public function update(int $id, string $name)
    {
        return Query::update('tag', ['id'=>$id, 'name'=>$name]);
    }

    public function getId(string $name)
    {
        return ($fetch=Query::where('tag', ['id'], ['name'=>$name])->fetch())?intval($fetch['id']):0;
    }

    public function countAdd(int $id)
    {
        return Query::update('tag', 'count=count+1', ['id'=>$id]);
    }

    public function list(int $page=1, int $count=10)
    {
        return Query::where('tag', ['id', 'name', 'count'], '1', [], [$page, $count])->fetchAll();
    }
}
