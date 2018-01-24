<?php
namespace cn\atd3\android;

use cn\atd3\proxy\ProxyObject;

class Message extends ProxyObject
{
    public function pull()
    {
        return [
            'message' => '服务器时间为：' .date('Y-m-d H:i:s') .', 你从服务器拉取了此条信息，其中信息数据库暂时没有弄。',
            'url'=>'https://github.com/DXkite/code4a_server',
            'time'=>10000,
            'color'=>'#222222',
            'backgroundColor'=>'#EEEEEE'
        ];
    }

    public function pullAd()
    {
        return [
            'image'=>'http://code4a.atd3.cn/ad.png',
            'url'=>'https://github.com/TTHHR/code4a',
        ];
    }
}
