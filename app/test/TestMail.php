<?php

$return=($mail=new Mail())
->from('usercenter@atd3.cn','用户中心')
->to('670337693@qq.com','DXkite')
->subject('DxCore 邮箱验证')
->use('mail')
->send([
    'site_name'=>'DxCore',
    'message'=>'欢迎注册DxCore账号！',
    'user'=>'DxCore用户',
    'verify'=>'//atd3.cn',
    'hosturl'=>'//atd3.cn',
    'hostname'=>'atd3.cn',
]);
var_dump($return);
var_dump($mail->errno(),$mail->error());