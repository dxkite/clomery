<?php
Page::json();
switch ($_GET['type']) {
    case 'signup':
    return api_check_callback($_POST, ['name', 'email', 'password'], function ($name, $email, $password) {
        $uid=User::signUp($name, $email, $password);
        if ($uid){
            return  ['uid'=>$uid] ;
        }
        return new api\Error('signupError','name or email is duplicate!');
    });
    case 'signin':
    return api_check_callback($_POST, ['name', 'password', 'string'=>['session','off'] ], function ($name, $email, $session) {
        $uid=User::signIn($name, $email, $session =='on');
        if ($uid){
            return  ['uid'=>$uid] ;
        }
        return new api\Error('passwordError','password error!');
    });
}
