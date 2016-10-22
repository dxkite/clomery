<?php

function sendtouser($uid)
{
    if ($info=Common_User::getInfo($uid)) {
        $return=($mail=new Mail())
                ->from('usercenter@atd3.cn', '用户中心')
                ->to($info['uid'],$info['uname'])
                ->subject('DxCore 邮箱验证')
                ->use('mail')
                ->send([
                    'title'=>'来至 DxCore 的验证邮箱',
                    'site_name'=>'DxCore',
                    'message'=>'欢迎注册DxCore账号！',
                    'user'=>$info['uname'],
                    'verify'=>Common_User::createVerify($uid),
                    'hosturl'=>'//atd3.cn',
                    'hostname'=>'atd3.cn',
                ]);
        var_dump($return);
        var_dump($mail->errno(), $mail->error());
    }
}
sendtouser(51);
