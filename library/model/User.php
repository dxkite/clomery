<?php
namespace model;
use dto\User as OUser;
use archive\Manager as OManager;

class User
{
    function  checkEmail(string $email):bool{
        return (new OManager(new OUser(['email'=>$email])))->find(['uid'])?true:false;
    }
    function  checkName(string $name):bool{
        return (new OManager(new OUser(['name'=>$name])))->find(['uid'])?true:false;
    }
}