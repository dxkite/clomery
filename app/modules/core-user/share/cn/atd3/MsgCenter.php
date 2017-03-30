<?php
namespace cn\atd3;

use suda\core\Query;

class MsgCenter
{
    // system
    const TYPE_SYSTEM=1;        // 系统消息
    // notify
    const TYPE_NOTIFY=2;        // 公告
    // send
    const TYPE_REPLY=3;         // 用户回复
    const TYPE_VOTE=4;          // 回复表态
    const TYPE_MESSAGE=5;       // 私信

    const STATE_UNREAD=0;       // 未读
    const STATE_READ=1;         // 已读
    const STATE_DELETE=2;       // 删除
    const STATE_REVOKE=3;       // 撤回

    // 发送系统消息
    public static function system(string $message, $group_id=null)
    {
        return self::broadcast(0, $message, $group_id, self::TYPE_SYSTEM);
    }

    // 发送通知消息
    public static function notify(int $send, string $message, $group_id=null)
    {
        return self::broadcast(0, $message, $group_id, self::TYPE_NOTIFY);
    }

    // 发送广播
    protected function broadcast(int $send, string $message, $group_id=null, int $type)
    {
        try {
            Query::begin();
            $data=Query::insert('notification_data', ['data'=>$message]);
            if (is_null($group_id)) {
                $id=Query::insert('notification', '(`send`,`recv`,`type`,`time`,`state`,`data`) SELECT :send,`id`,:type,:time,:state,:data FROM `#{user}` WHERE 1;', ['send'=>$send, 'data'=>$data, 'time'=>time(), 'type'=>$type, 'state'=>self::STATE_UNREAD]);
            } else {
                $array=$group_id;
                if (is_numeric($group_id) || is_string($group_id)) {
                    $array=[intval($group_id)];
                }
                $infield=Query::prepareIn('group_id', $array);
                $param=array_merge(['send'=>$send, 'data'=>$data, 'time'=>time(), 'type'=>$type, 'state'=>self::STATE_UNREAD], $infield['param']);
                $id=Query::insert('notification', '(`send`,`recv`,`type`,`time`,`state`,`data`) SELECT :send,`id`,:type,:time,:state,:data FROM `#{user}` WHERE '.$infield['sql'].';', $param);
            }
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $data;
    }

    // 发送私信
    public static function send(int $send, int $recv, int $type, string $message)
    {
        try {
            Query::begin();
            $data=Query::insert('notification_data', ['data'=>$message]);
            $id=Query::insert('notification', ['send'=>$send, 'recv'=>$recv, 'data'=>$data, 'time'=>time(), 'type'=>$type, 'state'=>self::STATE_UNREAD]);
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $id;
    }

    // 设置信息状态
    public static function setStatus(array $ids,int $recv,int $state=self::STATE_READ)
    {
        return Query::update('notification', ['state'=>$state], ['id'=>$ids,'recv'=>$recv]);
    }
    // 删除信息
    public static function delete(int $recv,array $ids)
    {
        return self::setStatus($ids,$recv,self::STATE_DELETE);
    }

    public static function inbox(int $recv, int $type, int $page=1, int $count=10)
    {
        if ($fetch=Query::select('notification', '`#{notification}`.`id`,`send`,`type`,`time`,`state`,`#{notification_data}`.`data` ', ' JOIN `#{notification_data}` ON `#{notification_data}`.`id`=`#{notification}`.`data` WHERE `recv` = :recv AND `type`=:type AND `state` != ' .self::STATE_DELETE, ['recv'=>$recv, 'type'=>$type], [$page, $count])->fetchAll()) {
            $ids=[];
            foreach ($fetch as $index=>$item){
                $ids[]=intval($item['id']);
                $fetch[$index]['id']=intval($item['id']);
                $fetch[$index]['send']=intval($item['send']);
                $fetch[$index]['type']=intval($item['type']);
                $fetch[$index]['state']=intval($item['state']);
            }
            self::setStatus($ids,$recv);
            return $fetch;
        }
        return false;
    }


    
    public static function listBroadcast(int $type, int $page=1, int $count=10)
    {
        if ($fetch=Query::where('notification', ['send', 'data'], ' type = :type AND state != ' .self::STATE_DELETE .' GROUP BY `data` ', ['type'=>$type], [$page, $count])->fetchAll()) {
            return $fetch;
        }
        return false;
    }

    public static function revokeById(int $id)
    {
        return Query::update('notification', ['state'=>self::STATE_REVOKE], ['id'=>$id]);
    }

    public static function revokeByData(int $data)
    {
        return Query::update('notification', ['state'=>self::STATE_REVOKE], ['data'=>$data]);
    }
}
