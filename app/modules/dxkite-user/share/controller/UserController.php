<?php
namespace dxkite\user\controller;

use dxkite\user\table\UserTable;
use dxkite\user\exception\UserException;
use suda\mail\message\Message;
use dxkite\support\file\File;
use dxkite\support\file\Media;
use dxkite\user\sms\SMSFactory;

class UserController
{
    protected $table;
    
    const TYPE_EMAIL=0;
    const TYPE_MOBILE=1;

    public function __construct()
    {
        $this->table = new UserTable;
    }
    
    public function getAccountId(string $account):?int
    {
        $user = $this->table->getByAccount($account);
        return $user?$user['id']:null;
    }

    public function get(int $userId):?array
    {
        return $this->table->select(['id','name','email','mobile','avatar','status'], ['id' => $userId])->fetch();
    }

    public function getPublicInfo(int $userId):?array
    {
        return $this->table->select(['id','name','avatar','status'], ['id' => $userId])->fetch();
    }

    public function getPublicInfoArray(array $userIds):?array
    {
        $userIds= \array_unique($userIds);
        return $this->table->select(['id','name','avatar','status'], ['id' => $userIds])->fetchAll();
    }

    public function signin(string $account, string $password, bool $remember=false)
    {
        $user = $this->table->getByAccount($account);
        if ($user) {
            if ($user['status'] != UserTable::STATUS_ACTIVE) {
                throw new UserException('account is not active', UserException::ACCOUNT_IS_NOT_ACTIVE);
            }
            if (password_verify($password, $user['password'])) {
                visitor()->signin($user['id'], 3600, $remember);
                return $user;
            }
        }
        throw new UserException('account or password error', UserException::ACCOUNT_OR_PASSWORD_ERROR);
    }

    public function checkNameExists(string $name)
    {
        return $this->table->getByName($name);
    }

    public function checkEmailExists(string $email)
    {
        return $this->table->getByEmail($email);
    }

    public function checkMobileExists(string $mobile)
    {
        return $this->table->getByMobile($mobile);
    }
 
    public function add(string $name, ?string $email, ?string $mobile, string $password):?int
    {
        $user= [
            'name'=>$name,
            'password'=>password_hash($password, PASSWORD_DEFAULT),
            'signupTime'=>time(),
            'signupIp'=> request()->ip(),
            'status'=>UserTable::STATUS_ACTIVE,
        ];

        if (self::checkNameExists($name)) {
            throw new UserException('user name exsit', UserException::NAME_EXISTS_ERROR);
        }
        if (!is_null($mobile)) {
            if (self::checkMobileExists($mobile)) {
                throw new UserException('user mobile exsit', UserException::MOBILE_EXISTS_ERROR);
            } else {
                $user['mobile'] = $mobile;
            }
        }
        if (!is_null($email)) {
            if (self::checkEmailExists($email)) {
                throw new UserException('user email exsit', UserException::EMAIL_EXISTS_ERROR);
            } else {
                $user['email'] = $email;
            }
        }
        return $this->table->insert($user);
    }

    public function edit(int $id, array $sets)
    {
        if (array_key_exists('name', $sets)) {
            $name = $sets['name'];
            if (self::checkNameExists($name)) {
                throw new UserException('user name exsit', UserException::NAME_EXISTS_ERROR);
            }
        }
        if (array_key_exists('email', $sets)) {
            $email = $sets['email'];
            if (self::checkEmailExists($email)) {
                throw new UserException('user email exsit', UserException::EMAIL_EXISTS_ERROR);
            }
        }
        if (array_key_exists('mobile', $sets)) {
            $mobile = $sets['mobile'];
            if (self::checkMobileExists($mobile)) {
                throw new UserException('user mobile exsit', UserException::MOBILE_EXISTS_ERROR);
            }
        }
        if (array_key_exists('password', $sets)) {
            $password = $sets['password'];
            $sets['password']=password_hash($password, PASSWORD_DEFAULT);
        }
        return $this->table->updateByPrimaryKey($id, $sets);
    }

    public function signout()
    {
        // 用户中心注销
        visitor()->signout();
    }

    public function sendPasswordVerifyEmail(string $email):?bool
    {
        $userData = $this->table->getByEmail($email);
        if ($userData) {
            $code = random_int(100000, 999999);
            $message = new Message(__('找回密码'), __('你本次密码找回的验证码为:%s,请在5分钟内完成验证', $code));
            $message->setFrom('mail_poster@'.request()->getHost(), __('ATD用户管理中心'));
            $message->setTo($userData['email'], $userData['name']);
            try {
                $return  = support_mailer()->send($message);
            } catch (\Exception $e) {
                debug()->error('邮件发送失败', $e->getMessage());
            }
            if ($return) {
                return $this->table->updateByPrimaryKey($userData['id'], [
                    'code'=>$code,
                    'codeLive'=>time()+ 5*60,
                ]) > 0;
            }
            return false;
        }
        return null;
    }
    
    public function verifyCodeByEmail(string $email, string $code)
    {
        $user= $this->table->getByEmail($email);
        if ($user) {
            return $user['codeLive'] >  time() && $user['code'] == $code;
        }
        return false;
    }

    public function sendPasswordVerifyMobile(string $mobile):?bool
    {
        $userData = $this->table->getByMobile($mobile);
        if ($userData) {
            $code = random_int(100000, 999999);
            if (time() - $userData['mobileSended'] > 60) {
                $sender = SMSFactory::sender();
                if ($sender) {
                    $return= $sender->send($userData['mobile'], '找回密码', $code);
                } else {
                    return false;
                }
            } else {
                return false;
            }
            if ($return) {
                return $this->table->updateByPrimaryKey($userData['id'], [
                    'code'=>$code,
                    'codeLive'=>time()+ 5*60,
                ]) > 0;
            }
            return false;
        }
        return null;
    }

    public function verifyCodeByMobile(string $mobile, string $code)
    {
        $user= $this->table->getByMobile($mobile);
        if ($user) {
            return $user['codeLive'] >  time() && $user['code'] == $code;
        }
        return false;
    }

    public function sendVerifyEmail(int $userid):?bool
    {
        $userData = $this->table->select('*', ['id'=>$userid])->fetch();
        if ($userData) {
            $code = random_int(100000, 999999);
            $message = new Message(__('邮箱验证'), __('你本次验证邮箱的验证码为:%s,请在5分钟内完成验证', $code));
            $message->setFrom('mail_poster@'.request()->getHost(), __('ATD用户管理中心'));
            $message->setTo($userData['email'], $userData['name']);
            try {
                debug()->info(__('send_email %s %s', $userData['email'], $code));
                $return  = support_mailer()->send($message);
            } catch (\Exception $e) {
                debug()->error('邮件发送失败', $e->getMessage());
            }
            if ($return) {
                return $this->table->updateByPrimaryKey($userData['id'], [
                    'code'=>$code,
                    'codeLive'=>time()+ 5*60,
                ]) > 0;
            }
            return false;
        }
        return null;
    }

    public function sendVerifyMessage(int $userid):?bool
    {
        $userData = $this->table->select('*', ['id'=>$userid])->fetch();
        if ($userData) {
            // 验证码发送时间限制
            $code = random_int(100000, 999999);
            if (time() - $userData['mobileSended'] > 60) {
                $return= SMSFactory::sender()->send($userData['mobile'], '账号验证', $code);
            } else {
                return false;
            }
            if ($return) {
                return $this->table->updateByPrimaryKey($userData['id'], [
                    'mobileSended' => time(),
                    'code'=>$code,
                    'codeLive'=>time()+ 5*60,
                ]) > 0;
            }
            return false;
        }
        return null;
    }
    
    public function verifyCodeById(int $userId, string $code)
    {
        $user= $this->table->getByPrimaryKey($userId);
        if ($user) {
            return $user['codeLive'] >  time() && $user['code'] == $code;
        }
        return false;
    }

    public function setVerifyed(int $user, int $type)
    {
        $data = [
            'code'=> null,
            'codeLive'=> null,
        ];
        if ($type == self::TYPE_EMAIL) {
            $data['emailChecked']=1;
        } else {
            $data['mobileChecked']=1;
        }
        return $this->table->updateByPrimaryKey($user, $data) > 0;
    }

    public function cleanVerifyCode(int $user)
    {
        return $this->table->updateByPrimaryKey($user, [
            'code'=> null,
            'codeLive'=> null,
        ]) > 0;
    }

    public function cleanVerifyCodeByEmail(string $email)
    {
        $user= $this->table->getByEmail($email);
        return $this->table->updateByPrimaryKey($user['id'], [
            'code'=> null,
            'codeLive'=> null,
        ]) > 0;
    }

    public function cleanVerifyCodeByMobile(string $mobile)
    {
        $user= $this->table->getByMobile($mobile);
        return $this->table->updateByPrimaryKey($user['id'], [
            'code'=> null,
            'codeLive'=> null,
        ]) > 0;
    }

    public function setPasswordByEmail(string $email, string $password):bool
    {
        return $this->table->changePasswordByEmail($email, $password) > 0;
    }

    public function setPasswordByMobile(string $mobile, string $password):bool
    {
        return $this->table->changePasswordByMobile($mobile, $password) > 0;
    }

    public function setPassword(int $user, string $password):bool
    {
        return $this->table->changePassword($user, $password) > 0;
    }
    
    public function setAvatar(int $user, File $file)
    {
        $fileInfo=(new Media)->saveFile($file);
        if ($fileInfo) {
            return  $this->table->updateByPrimaryKey($user, ['avatar'=>$fileInfo->getId()]);
        }
        return false;
    }
    
    public function checkPasswordByMobile(string $mobile, string $password):bool
    {
        $user= $this->table->getByMobile($mobile);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                return true;
            }
        }
        return false;
    }
    public function checkPasswordByEmail(string $email, string $password):bool
    {
        $user= $this->table->getByEmail($email);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                return true;
            }
        }
        return false;
    }

    public function resetPassword(int $id, string $oldPassword, string $password)
    {
        if ($this->table->checkPassword($id, $oldPassword)) {
            return  $this->table->updateByPrimaryKey($id, ['password'=>password_hash($password, PASSWORD_DEFAULT) ]);
        }
        throw new UserException('password error', UserException::ACCOUNT_OR_PASSWORD_ERROR);
    }
}
