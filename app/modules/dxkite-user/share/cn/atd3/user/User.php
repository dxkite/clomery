<?php
namespace cn\atd3\user;

use cn\atd3\visitor\Visitor;
use cn\atd3\visitor\Permission;

class User extends Visitor
{
    // 检查是否是登陆状态
    protected function check(int $id, string $token)
    {
        $check=Manager::checkTokenVaild($id, $token);
        if ($check) {
            debug()->trace(__('__get_permission %d',$id),Manager::getPermissonsByUserId($id));
            $permission=Manager::getPermissonsByUserId($id);
            $this->setPermission(new Permission($permission));
        }
        return $check;
    }
}
