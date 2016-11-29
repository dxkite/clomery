<?php
namespace model;

use Query;

class Upload
{
    const STATE_VERIFY=0;
    const STATE_PUBLISH=1;
    const STATE_PRIVATE=2;
    const STATE_DELETE=3;
    
    public function create(int $uid, string $name, int $size, string $type, string $hash, int $use=0, int $state =self::STATE_PUBLISH)
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
            if ($get=Query::where('upload', 'id', ['data'=>$data, 'use'=>0])->fetch()) {
                $id=$get['id'];
            } else {
                $id = Query::insert('upload', ['uid'=>$uid, 'name'=>$name, 'size'=>$size, 'time'=>time(), 'type'=>$type, 'data'=>$data, 'use'=>$use, 'state'=>$state]);
            }
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $id;
    }

    public function setUse(int $id)
    {
        return Query::update('upload', ['use'=>1], ['id'=>$id]);
    }

    public function setUseByIds(array $ids)
    {
        $in=Query::prepareIn('id',$ids);
        return Query::update('upload', ['use'=>1],$in['sql'],$in['param']);
    }

    public function delete(int $id)
    {
        return Query::update('upload', ['state'=>self::STATE_DELETE], ['id'=>$id]);
    }

    public function get(int $id)
    {
        if ($select=Query::select('upload', ['uid', 'name', 'size', 'time', 'type', 'hash', 'use', 'state'], ' JOIN `#{upload_data}` ON `#{upload_data}`.`id` = `data` WHERE `#{upload}`.`id`=:id AND `state`=:state ', ['id'=>$id])->fetch()) {
            return $select;
        }
        return false;
    }

    public function getWhen(int $id, int $state=self::STATE_PUBLISH)
    {
        if ($select=Query::select('upload', ['uid', 'name', 'size', 'time', 'type', 'hash', 'use'], ' JOIN `#{upload_data}` ON `#{upload_data}`.`id` = `data` WHERE `#{upload}`.`id`=:id AND `state`=:state ',['id'=>$id, 'state'=> $state])->fetch()) {
            return $select;
        }
        return false;
    }

    public function update(int $id, int $uid, string $name, int $size, int $time, string $type, int $data, int $use, int $state)
    {
        return Query::update('upload', ['id'=>$id, 'uid'=>$uid, 'name'=>$name, 'size'=>$size, 'time'=>$time, 'type'=>$type, 'data'=>$data, 'use'=>$use, 'state'=>$state]);
    }

    public function list(int $page=1, int $count=10)
    {
        return Query::where('upload', ['id', 'uid', 'name', 'size', 'time', 'type', 'data', 'use', 'state'], '1', [], [$page, $count])->fetchAll();
    }
}
