<?php

$return=($mail=new Mail())
->from('mail@atd3.cn','UserCenter')
->to('670337693@qq.com','DXkite')
->subject('发送邮件测试')
->use('mail')
->assign('message','Dxkite Send At:'.date('Y-m-d H:i:s').',测试中文')
->send();
var_dump($return);
var_dump($mail->errno(),$mail->error());