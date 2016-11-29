<?php

namespace model;

use Query;

class Setting
{
                                                                                                                
    public function create(string $name,string $type,string $value)
    {
        return Query::insert('site_setting',['name'=>$name,'type'=>$type,'value'=>$value]);
    }

    public function delete(int $id){
        return Query::delete('site_setting',['id'=>$id]);
    }

    public function update(int $id,string $name,string $type,string $value){
       return Query::update('site_setting',['id'=>$id,'name'=>$name,'type'=>$type,'value'=>$value]); 
    }

    public function get(){
        return Query::where('site_setting', ['id','name','type','value'], '1')->fetchAll();
    }
}