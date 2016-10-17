<?php
    require_once 'core/XCore.php';
    //require_once 'core/WebSocket.php';
    WebSocket::onMessage(function ($id,$message) {  
        $tst_msg = json_decode($message);
        var_dump($tst_msg);
        $user_name = $tst_msg->name;
        $user_message = $tst_msg->message;
        WebSocket::pushMessage(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message)));
    });
    WebSocket::listen();
