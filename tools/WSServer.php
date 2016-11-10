<?php
require_once __DIR__.'/../core/XCore.php';
        // 开启Session
    Session::start();
    //require_once 'core/WebSocket.php';
    $ws=new WebSocket_Server();
    $ws->onMessage(function ($id, $message) use($ws) {
        $tst_msg = json_decode($message);
        if ($tst_msg) {
            var_dump($tst_msg);
            $user_name = $tst_msg->name;
            $user_message = $tst_msg->message;
        }
        $ws->pushMessage(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message)));
    });
    $ws->listen();
