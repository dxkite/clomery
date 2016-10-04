<?php
namespace user;

use Page;
use Request;
use Query;

class ajax
{
    public $error=[
        -1=>'unsupport json',
    ];
    public function main()
    {
        Page::getController()->json();
        $json=Request::json();
        if (isset($json['type'])) {
            switch ($json['type']) {
                case 'checkuser': return ['return'=>self::checkuser($json['user'])];break;
            }
        } else {
            return ['return'=>-1,'message'=>$this->error[-1]];
        }
        return ['return'=>0,'message'=>'no error'];
    }
    public function checkuser(string $user)
    {
        $q=new Query('SELECT uid FROM #{users} where LOWER(uname) = LOWER(:uname) LIMIT 1;');
        $q->values(['uname'=>$user]);
        if ($get=$q->fetch()){
            return $get;
        }
        return false;
    }
}
