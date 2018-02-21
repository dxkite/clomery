<?php
namespace dxkite\android\message;

use dxkite\support\proxy\ProxyObject;

class SuperUser
{
    /**
     * 使用超级权限模拟用户
     * @paramSource JSON,GET,POST
     * @param string $token 令牌
     * @param integer $userId  被模拟的用户ID
     * @return bool 
     */
    public function su(string $token, int $userId = 0)
    {
        if ($token == setting('super-user-token')) {
            visitor()->signin(setting('super-user-id'));
            if ($userId) {
                visitor()->simulateUser($userId);
                return true;
            } else {
                return visitor()->getId() == setting('super-user-id');
            }
        }
        return false;
    }
}
