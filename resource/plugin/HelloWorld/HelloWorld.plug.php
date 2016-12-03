<?php

namespace Plugin;

class HelloWorld implements \PlugInterface
{
    // 装载插件
    public static function mount()
    {
        echo __METHOD__."<br/>\r\n";
    }
    // 卸载插件
    public static function umount()
    {
        echo __METHOD__."<br/>\r\n";
    }
     // 启动插件
    public static function boot()
    {
       echo __METHOD__."<br/>\r\n";
    }
     // 关闭插件
    public static function shutdown()
    {
        echo __METHOD__."<br/>\r\n";
    }
}
