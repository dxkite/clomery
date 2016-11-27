<?php
namespace model;
use dto\User as OUser;
use archive\Manager as OManager;

class User
{
    function  checkEmail(string $email):bool{
        // Query::where('user',['uid'],'email = :email',['email'=>$email]);
        return (new OManager(new OUser(['email'=>$email])))->find(['uid'])?true:false;
    }
    function  checkName(string $name):bool{
        return (new OManager(new OUser(['name'=>$name])))->find(['uid'])?true:false;
    }
    function count()
    {
        return (new OManager(new OUser()))->count();
    }
}