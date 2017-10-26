<?php
namespace cn\atd3\user;

use cn\atd3\visitor\Visitor;
use cn\atd3\visitor\Permission;
use cn\atd3\visitor\Context;

class User extends Visitor
{
    // 检查是否是登陆状态
    protected function check(int $id, string $token)
    {
        $check=Manager::checkTokenVaild($id, $token);
        if ($check) {
            debug()->trace(__('__get_permission %d', $id), Manager::getPermissonsByUserId($id));
            $permission=Manager::getPermissonsByUserId($id);
            $this->setPermission(new Permission($permission));
        }
        return $check;
    }

    public function sign(int $id,bool $remember)
    {
        // 生成TOKEN
        $token=md5($account.microtime());
        $token_expire=time()+($remember?conf('system.user_remember', 86400):conf('system.user_expire', 86400));
        // 刷新
        Manager::refershToken($id, $token, $token_expire);
        $this->refresh($id, $token);
        $this->id=$id;
        $this->token=$token;
        Context::getInstance()->cookieVisitor($this)->expire($token_expire)->session(!$remember)->set();
        return $this;
    }
}
