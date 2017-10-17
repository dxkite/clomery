<?php
namespace cn\atd3\visitor;

use suda\core\Session;


abstract class Visitor
{
    protected $id;
    protected $token;
    protected $permission=null;
    protected $isGuest=true;

    const  MASK=0x23333333;

    public function __construct(string $token=null)
    {
        
        if ($token&&$get=self::decodeToken($token)) {
            list($this->id, $this->token)=$get;
            $this->isGuest=!$this->check($this->id, $this->token);
        } else {
            $this->isGuest=true;
        }
        if ($this->isGuest) {
            $this->id=0;
            $this->token=md5('Guest-User');
            $this->permission=new Permission;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function refresh(int $id, string $token)
    {
        $this->id=$id;
        $this->token=$token;
        return $this;
    }

    public function setPermission(Permission $permission)
    {
        $this->permission=$permission;
        return $this;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function isGuest()
    {
        return $this->isGuest;
    }

    public function getMaskToken()
    {
        return $this->encodeToken($this->id, $this->token);
    }

    public function canAccess($method)
    {
        return $this->hasPermission(Permission::createFromFunction($method));
    }

    public function hasPermission($permission)
    {
        if(!$permission instanceof Permission){
            if(is_array($permission)){
                $permission=new Permission($permission);
            }elseif(is_string($permission)){
                $permission=new Permission([$permission]);
            }else{
                $permission=new Permission;
            }
        }
        $check=$this->getPermission()->surpass($permission);
        debug()->trace(__('check_access %d',$check),['visitor'=>$this->getPermission(),'need'=>$permission]);
        return $check;
    }

    private static function encodeToken(int $id, string $token)
    {
        // 32-bit-Pack
        $idnum=bin2hex(pack('N', $id^Visitor::MASK));
        return base64_encode(hex2bin($idnum.$token));
    }

    private static function decodeToken(string $tokenstr)
    {
        $tokenstr=bin2hex(base64_decode($tokenstr));
        if (strlen($tokenstr)!=40) {
            return false;
        }
        $idnum=substr($tokenstr, 0, 8);
        $id=unpack('Nid', hex2bin($idnum))['id'];
        return [$id^Visitor::MASK,substr($tokenstr, 8, 32)];
    }
    
    // 检查是否是登陆状态
    abstract protected function check(int $id, string $token);
}
