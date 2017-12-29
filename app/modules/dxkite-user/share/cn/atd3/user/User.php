<?php
namespace cn\atd3\user;

use cn\atd3\visitor\Visitor;
use cn\atd3\visitor\Permission;
use cn\atd3\visitor\Context;

class User extends Visitor
{
    const  simulateUserToken='_simulate_user_id';
    protected $simulate=null;
    // 检查是否是登陆状态
    protected function check(int $id, string $token)
    {
        $check=Manager::checkTokenVaild($id, $token);
        if ($check) {
            $this->refreshPermission($id);
        }
        return $check;
    }

    public function getId()
    {
        if ($this->hasPermission('admin:user.simulate') && cookie()->has(User::simulateUserToken)) {
            $userId=cookie()->get(User::simulateUserToken,$this->id);
            debug()->trace(__( 'user_simulated  %d --> %d',$this->id ,$userId));
            if ($this->simulate != $userId){
                $this->refreshPermission($userId);
            }
            return $userId;
        }
        return $this->simulate ?? $this->id;
    }

    public function sign(int $id, bool $remember)
    {
        // 生成TOKEN
        $token=md5($id.microtime());
        $token_expire=time()+($remember?conf('system.user_remember', 86400):conf('system.user_expire', 86400));
        // 刷新
        Manager::refershToken($id, $token, $token_expire);
        $this->refresh($id, $token);
        Context::getInstance()->cookieVisitor($this)->expire($token_expire)->session(!$remember)->set();
        return $this;
    }

    protected function refreshPermission(int $id){
        $permission=Manager::getPermissonsByUserId($id);
        debug()->trace(__('refresh_permission_for_user %d', $id), $permission);
        $this->simulate = $id;
        if ($permission) {
            $this->setPermission(new Permission($permission));
        } else {
            $this->setPermission(new Permission);
        }
    }
}
