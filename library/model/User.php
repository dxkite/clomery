<?php
namespace model;

use Query;
use Request;

class User
{
    public function checkEmail(string $email):bool
    {
        return Query::where('user', 'uid', 'LOWER(email) = LOWER(:email)', ['email'=>$email])->fetch()?true:false;
    }

    public function checkName(string $name):bool
    {
        return Query::where('user', 'uid', 'LOWER(name) = LOWER(:name)', ['name'=>$name])->fetch()?true:false;
    }
    
    public function count()
    {
        return Query::count('user');
    }

    public function signUp(string $name, string $email, string $password, string $usage='web-signup')
    {
        try {
            Query::begin();
            $uid=Query::insert('user', ['name'=>$name, 'password'=>password_hash($password, PASSWORD_DEFAULT), 'email'=>$email]);
            $token=Token::createToken($uid, $usage);
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $token;
    }

    public function signIn(string $name, string $password, string $usage='web-signin')
    {
        $token=false;
        try {
            Query::begin();
            if ($fetch=Query::where('user', ['password', 'uid'], ['name'=>$name])->fetch()) {
                if (password_verify($password, $fetch['password'])) {
                    $token=Token::createToken($fetch['uid'], $usage);
                }
            }
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return $token;
    }

    public function signOut(int $uid, string $token)
    {
        return Token::deleteToken($uid, $token);
    }
    
    public function isSignin(int $uid, string $token)
    {
        return Token::verifyToken($uid, $token);
    }
}
