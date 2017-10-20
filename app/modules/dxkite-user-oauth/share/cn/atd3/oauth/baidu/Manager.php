<?php
namespace cn\atd3\oauth\baidu;
use suda\template\Manager as TemplateManger;
class Manager {
    public static function adminItem($template){
        TemplateManger::include('user-oauth:baidu/setting',$template)->render();
    }
}