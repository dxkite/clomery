<?php
namespace cn\atd3\user;

use cn\atd3\exception\UserException;
use cn\atd3\user\dao\UserDAO;
use cn\atd3\user\dao\GroupDAO;
use suda\core\Request;
require_once __DIR__.'/function.php';

class Manager
{
    const ACTIVE=1;
    const FREEZE=0;
    const EXISTS_USER=-4;
    const EXISTS_EMAIL=-5;

    /**
     * 注册用户
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    public static function add(string $name, string $email, string $password, string $token='', string $valid_expire='')
    {
        if (self::checkNameExists($name)) {
            return Manager::EXISTS_USER;
        }
        if (self::checkEmailExists($email)) {
            return Manager::EXISTS_EMAIL;
        }
        return (new UserDAO)->insert([
            'name'=>$name,
            'email'=>$email,
            'password'=>password_hash($password, PASSWORD_DEFAULT),
            'signup_time'=>time(),
            'signup_ip'=>Request::ip(),
            'status'=>Manager::ACTIVE,
            'valid_token'=>$token,
            'valid_expire'=>$valid_expire,
        ]);
    }


    public static function id2name(int $id)
    {
        return (new UserDAO)->setFields(['name'])->getByPrimaryKey($id)['name']??__('佚名');
    }
    
    public static function ids2name(array $id)
    {
        $ids=(new UserDAO)->setFields(['id','name'])->listWhere(['id'=>$id]);
        $return=[];
        foreach ($ids as $res) {
            $return[$res['id']]=$res['name'];
        }
        return $return;
    }

    public static function getUserInfoById(int $id)
    {
        return (new UserDAO)->getInfo($id);
    }

    public static function getPermissonsByUserId(int $userid)
    {
        $gid=(new UserDAO)->setFields(['group_id'])->getByPrimaryKey($userid);
        if ($gid) {
            return (new GroupDAO)->getPermission($gid['group_id']);
        }
        return null;
    }

    public static function checkPermissions(int $userid, array $permission)
    {
        $gid=(new UserDAO)->setFields(['group_id'])->getByPrimaryKey($userid);
        if ($gid) {
            return (new GroupDAO)->checkPermission($gid['group_id'], $permission);
        }
        return null;
    }
    
    public static function groups2name(array $id=null):array
    {
        if (is_null($id)) {
            $gid=(new GroupDAO)->list();
        } else {
            $gid=(new GroupDAO)->groups2name($id);
        }
        $group=[];
        if (is_array($gid)) {
            foreach ($gid as $id=>$value) {
                $group[$value['id']]=$value['name'];
            }
        }
        return $group;
    }

    public static function group2name(int $id)
    {
        $gid=(new GroupDAO)->setFields(['name'])->getByPrimaryKey($id);
        if ($gid) {
            return $gid->fetch()['name'];
        }
        return null;
    }

    public static function getIdByEmail(string $email)
    {
        return (new UserDAO)->getByEmail($email);
    }

    public static function getIdByName(string $name)
    {
        return (new UserDAO)->getByName($name);
    }

    public static function changePassword(int $userid, string $oldpasswd, string $password)
    {
        self::throwablePassworld($userid, $oldpasswd);
        return (new UserDAO)->updateByPrimaryKey($userid, [
            'password'=>password_hash($password, PASSWORD_DEFAULT),
            'valid_token'=>'',
            'valid_expire'=>'',
        ]);
    }

    public static function checkPassword(int $id, string $password)
    {
        if ($user=(new UserDAO)->setFields(['password'])->getByPrimaryKey($id)) {
            if (password_verify($password, $user['password'])) {
                return true;
            }
        }
        return false;
    }

    public static function checkNameExists(string $name)
    {
        return (new UserDAO)->getByName($name);
    }

    public static function checkEmailExists(string $email)
    {
        return (new UserDAO)->getByEmail($email);
    }

    public static function checkTokenVaild(int $uid, string $token)
    {
        if ($user=(new UserDAO)->select(['valid_expire'], ['id'=>$uid,'valid_token'=>$token])->fetch()) {
            return $user['valid_expire']>time();
        }
        return false;
    }

    public static function refershToken(int $uid, string $token, int $valid_expire)
    {
        return (new UserDAO)->updateByPrimaryKey($uid, [
            'valid_token'=>$token,
            'valid_expire'=>$valid_expire,
        ]);
    }
}
