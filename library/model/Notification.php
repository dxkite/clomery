<?php
namespace model;

class Notification
{
    const SYSTEM=1;     // 系统消息
    const NOTIFY=2;     // 公告
    const REPLY=3;     // 用户回复
    const VOTE=4;  // 回复表态
    const MESSAGE=5;    // 私信
    // 发送系统消息
    public function system(string $message, $recv_id=null)
    {
        if (is_null($recv_id)) {
            try {
                Query::begin();
                $data=Query::insert('notification_data', ['data'=>$message]);
                Query::insert('notification','',['data'=>$data,'time'=>$time,'type'=>self::SYSTEM]);
            } catch (\Exception $e) {
                Query::rollBack();
                return false;
            }
        }
    }
}
