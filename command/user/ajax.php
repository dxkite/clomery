<?php


switch ($_GET['type'])
{
    case 'signup':
    $uid=User::signUp($_POST['name'],$_POST['email'],$_POST['password']);
    echo json_encode(['uid'=>$uid]);
    break;
    case 'signin':
    $uid=User::signIn($_POST['name'],$_POST['password']);
    echo json_encode(['uid'=>$uid]);
    
}
