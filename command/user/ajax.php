<?php

$client=Kite::getClient();
switch ($_GET['type'])
{
    case 'signup':
    echo json_encode(model\User::signUp($_POST['name'],$_POST['email'],$_POST['password'],$client['id'],$client['token']));
}
