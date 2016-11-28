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
    public function system(string $message,int $recv_id)
    {
        
    }
}
