<?php

namespace model;

use Query;

class Setting
{
    public function set(string $name, string $value)
    {
        try {
            if (!self::update($name, $value)) {
                return Query::insert('site_setting', ['name'=>$name, 'value'=>$value]);
            }
        } catch (\Exception $e) {
            return true;
        }
        return true;
    }

    public function delete(int $id)
    {
        return Query::delete('site_setting', ['id'=>$id]);
    }

    protected function update(string $name, string $value)
    {
        return Query::update('site_setting', ['value'=>$value], ['name'=>$name]);
    }

    public function get(string $name)
    {
        return Query::where('site_setting', ['id', 'name', 'value'], ['name'=>$name])->fetch();
    }

    public function getAll()
    {
        return Query::where('site_setting', ['id', 'name', 'value'])->fetchAll();
    }
}
