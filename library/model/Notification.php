<?php
namespace model;

use Query;

class Notification
{
    // system
    const TYPE_SYSTEM=1;        // 系统消息
    // notify
    const TYPE_NOTIFY=2;        // 公告
    // send
    const TYPE_REPLY=3;         // 用户回复
    const TYPE_VOTE=4;          // 回复表态
    const TYPE_MESSAGE=5;       // 私信

    const STATE_UNREAD=0;      // 未读
    const STATE_READ=1;       // 已读
    const STATE_DELETE=2;       // 删除

    // 发送系统消息
    public function system(string $message, $group_id=null)
    {
        return self::broadcast(0,$message,$group_id,self::TYPE_SYSTEM);
    }

    // 发送通知消息
    public function notify(int $send,string $message, $group_id=null)
    {
        return self::broadcast(0,$message,$group_id,self::TYPE_NOTIFY);
    }

    // 发送广播
    protected function broadcast(int $send,string $message, $group_id=null,int $type)
    {
        try {
            Query::begin();
            $data=Query::insert('notification_data', ['data'=>$message]);
            if (is_null($group_id)) {
                $id=Query::insert('notification', '(`send`,`recv`,`type`,`time`,`state`,`data`) SELECT :send,`id`,:type,:time,:state,:data FROM `#{user}` WHERE 1;', ['send'=>$send,'data'=>$data, 'time'=>time(), 'type'=>$type, 'state'=>self::STATE_UNREAD]);
            } else {
                $array=$group_id;
                if (is_numeric($group_id) || is_string($group_id)) {
                    $array=[intval($group_id)];
                }
                $infield=Query::prepareIn('group_id', $array);
                $param=array_merge(['send'=>$send,'data'=>$data, 'time'=>time(), 'type'=>$type , 'state'=>self::STATE_UNREAD], $infield['param']);
                $id=Query::insert('notification', '(`send`,`recv`,`type`,`time`,`state`,`data`) SELECT :send,`id`,:type,:time,:state,:data FROM `#{user}` WHERE '.$infield['sql'].';', $param);
            }
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $id;
    }

    // 发送私信
    public function send(int $send, int $recv, int $type , string $message)
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
    public function setState(int $id,int $state=self::STATE_READ)
    {
        return Query::update('notification',['state'=>$state],['id'=>$id]);
    }

    public function delete(int $id){
        return self::setState($id,self::STATE_DELETE);
    }
}
