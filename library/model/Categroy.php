<?php
namespace model;

use Query;

class Categroy
{
    public function create(int $icon,string $name,string $slug,string $discription,int $sort=0,int $parent=0)
    {
        return Query::insert('categroy',['icon'=>$icon,'name'=>$name,'slug'=>$slug,'discription'=>$discription,'sort'=>$sort,'parent'=>$parent]);
    }

    public function delete(int $id){
        return Query::delete('categroy',['id'=>$id]);
    }

    public function update(int $id,string $name,string $slug,string $discription,int $sort=0,int $parent=0){
       return Query::update('categroy',['icon'=>$icon,'name'=>$name,'slug'=>$slug,'discription'=>$discription,'sort'=>$sort,'parent'=>$parent],['id'=>$id]); 
    }
    
    public function list(int $page=1, int $count=10)
    {
        return Query::where('categroy', ['id','icon','name','slug','discription','sort','parent'], '1', [], [$page, $count])->fetchAll();
    }
}