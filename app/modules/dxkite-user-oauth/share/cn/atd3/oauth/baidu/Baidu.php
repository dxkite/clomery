<?php
namespace cn\atd3\oauth\baidu;

class Baidu {
    protected $getLoggedInUser='https://openapi.baidu.com/rest/2.0/passport/users/getLoggedInUser';
    protected $accessToken=null;
    public function __construct(string $token){
        $this->accessToken=$token;
    }    

    public function getLoggedInUser() {
        $data=Manager::curl($this->getLoggedInUser.'?access_token='.$this->accessToken);
        if ($data){
            return json_decode($data,true);
        }
        return false;
    }
}