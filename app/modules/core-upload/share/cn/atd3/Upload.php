<?php
namespace cn\atd3;

use Query;

class Upload
{
    const STATE_VERIFY=0;
    const STATE_PUBLISH=1;
    const STATE_PRIVATE=2;
    const STATE_DELETE=3;
    
    public static function create(int $uid, string $name, int $size, string $type, string $hash,int $state =self::STATE_PUBLISH)
    {
        try {
            Query::begin();
            if ($get=Query::where('upload_data', 'id', ['hash'=>$hash])->fetch()) {
                $data=$get['id'];
                Query::update('upload_data', 'ref = ref + 1', ['id' =>$get['id'] ]);
            } else {
                $data= Query::insert('upload_data', ['hash'=>$hash, 'ref'=>1]);
            }
            // 未使用的同一个文件则继续返回
            if ($get=Query::where('upload', 'id', ['data'=>$data])->fetch()) {
                $id=$get['id'];
            } else {
                $id = Query::insert('upload', ['uid'=>$uid, 'name'=>$name, 'size'=>$size, 'time'=>time(), 'type'=>$type, 'data'=>$data,  'state'=>$state]);
            }
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $id;
    }


    public static function delete(int $id)
    {
        return Query::update('upload', ['state'=>self::STATE_DELETE], ['id'=>$id]);
    }

    public static function get(int $id,int $state=self::STATE_PUBLISH)
    {
        if ($select=Query::select('upload', ['uid', 'name', 'size', 'time', 'type', 'hash', 'state'], ' JOIN `#{upload_data}` ON `#{upload_data}`.`id` = `data` WHERE `#{upload}`.`id`=:id AND `state`=:state ', ['id'=>$id,'state'=>$state])->fetch()) {
            return $select;
        }
        return false;
    }

    public static function getWhen(int $id, int $state=self::STATE_PUBLISH)
    {
        if ($select=Query::select('upload', ['uid', 'name', 'size', 'time', 'type', 'hash'], ' JOIN `#{upload_data}` ON `#{upload_data}`.`id` = `data` WHERE `#{upload}`.`id`=:id AND `state`=:state ',['id'=>$id, 'state'=> $state])->fetch()) {
            return $select;
        }
        return false;
    }

    public static function update(int $id, int $uid, string $name, int $size, int $time, string $type, int $data, int $state)
    {
        return Query::update('upload', ['id'=>$id, 'uid'=>$uid, 'name'=>$name, 'size'=>$size, 'time'=>$time, 'type'=>$type, 'data'=>$data, 'state'=>$state]);
    }

    public static function list(int $page=1, int $count=10)
    {
        return Query::where('upload', ['id', 'uid', 'name', 'size', 'time', 'type', 'data', 'state'], '1', [], [$page, $count])->fetchAll();
    }
}
