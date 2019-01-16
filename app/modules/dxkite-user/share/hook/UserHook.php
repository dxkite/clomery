<?php
namespace dxkite\user\hook;

use dxkite\user\table\UserTable;
use dxkite\support\visitor\Permission;
use dxkite\user\provider\UserProvider;
use dxkite\support\visitor\GrantManager;
use dxkite\user\controller\UserController;
use dxkite\support\table\visitor\RoleTable;
use dxkite\support\table\visitor\GrantTable;

class UserHook
{
    public static function initConfig()
    {
        config()->set('user-signin-route', module(__FILE__).':signin');
        config()->set('user-info-packer.pack', __CLASS__.'::pack');
        config()->set('user-info-packer.unpack', __CLASS__.'::unpack');
        config()->set('support.get-user-accountId', UserProvider::class.'->getAccountId');
        config()->set('support.get-user-public-info-array', UserProvider::class.'->getPublicInfoArray');
    }

    public static function pack(array $data)
    {
        if (array_key_exists('name', $data)) {
            return (new UserProvider) -> getAccountId($data['name']);
        }
        if (array_key_exists('email', $data)) {
            return (new UserProvider) -> getAccountId($data['email']);
        }
        return $data['id'] ?? null;
    }

    public static function unpack(int $userid)
    {
        return (new UserProvider) -> getPublicInfo($userid);;
    }
    
    public static function install()
    {
        $user = new UserController;
        (new UserTable)->truncate();
        (new GrantTable)->delete(1);
        (new RoleTable)->delete(1);
        $permissions=(new Permission)->getSystemPermissions();
        $id= (new GrantManager)->createRole('超级管理员', new Permission($permissions));
        $userId=$user->add('dxkite', 'dxkite@qq.com', null, 'dxkite');
        visitor()->signin($userId);
        (new GrantManager)->grant($id, $userId);
        visitor()->signin($userId);
    }
}
