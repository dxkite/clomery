<?php
namespace model;

use Query;
use Request;

class User
{
    /**
     * 验证邮箱
     */
    public function checkEmail(string $email):bool
    {
        return Query::where('user', 'id', 'LOWER(email) = LOWER(:email)', ['email'=>$email])->fetch()?true:false;
    }

    public function checkName(string $name):bool
    {
        return Query::where('user', 'id', 'LOWER(name) = LOWER(:name)', ['name'=>$name])->fetch()?true:false;
    }
    
    public function count()
    {
        return Query::count('user');
    }

    public function signUp(string $name, string $email, string $password, int $client_id, string $client_token, string $value='')
    {
        try {
            Query::begin();
            $id=Query::insert('user', ['name'=>$name, 'password'=>password_hash($password, PASSWORD_DEFAULT), 'email'=>$email]);
            $token=Token::create($id, $client_id, $client_token, $value);
            $token['user_id']=$id;
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $token;
    }

    public function signIn(string $name, string $password, int $client_id, string $client_token)
    {
        $token=false;
        try {
            Query::begin();
            if ($fetch=Query::where('user', ['password', 'id'], ['name'=>$name])->fetch()) {
                if (password_verify($password, $fetch['password'])) {
                    $token=Token::create($fetch['id'], $client_id, $client_token);
                    $token['user_id']=$fetch['id'];
                }
            }
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $token;
    }

    public function signOut(int $token_id, string $token)
    {
        return Token::delete($token_id, $token);
    }
    
    public function isSignin(int $token_id, string $token)
    {
        return Token::verify($token_id, $token);
    }

    // 心跳刷新
    public function heartBeat(int $token_id, string $token)
    {
        return Token::refresh($token_id, $token);
    }
    /**
    * @self:id
    */
    public function setAvatar(int $id, int $resource_id)
    {
        return Query::update('user', ['avatar'=>$resource_id], ['id'=>$id]);
    }
    /**
    * @Auth:admin
    */
    public function setGroup(int $id, int $group)
    {
        return Query::update('user', ['group'=>$group], ['id'=>$id]);
    }

    public function hasPermission(int $id, string $name)
    {
        // TODO :  list - permission
        try {
            if ($fetch=Query::select('user_group', $name, ' JOIN `#{user}` ON `#{user}`.`id` = :id  WHERE `user` = :id  or `#{user_group}`.`id` =`#{user}`.`group` LIMIT 1;', ['id'=>$id])->fetch()) {
                return $fetch[$name]=='Y';
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }
}
