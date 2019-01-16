<?php
namespace dxkite\user\table;

use suda\archive\Table;
use suda\core\Query;
use suda\core\Request;
use dxkite\user\exception\UserException;

class UserTable extends Table
{
    const STATUS_ACTIVE=1;
    const STATUS_FREEZE=0;
    const STATUS_DELETE=-1;

    const EMAIL_PREG='/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
    const MOBILE_PREG='/^(13[0-9]|14[5-9]|15[012356789]|166|17[0-8]|18[0-9]|19[8-9])[0-9]{8}$/';
    const NAME_PREG='/^[\w\x{4e00}-\x{9aff}]{4,255}$/u';

    public function __construct()
    {
        parent::__construct('user');
    }
    
    public function onBuildCreator($table)
    {
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('name', 'varchar', 255)->unique()->default(null)->comment("用户名"),
            // 注册凭证
            $table->field('email', 'varchar', 255)->unique()->default(null)->comment("邮箱"),
            $table->field('mobile', 'varchar', 255)->unique()->default(null)->comment("手机号"),
            // 验证方式
            $table->field('mobileChecked', 'tinyint', 0)->default(0)->comment("短信验证"),
            // 原则上一分钟一条
            $table->field('mobileSended', 'int', 11)->default(0)->comment("上次发送短信时间"),
            $table->field('emailChecked', 'tinyint', 0)->default(0)->comment("邮箱验证"),
            $table->field('code', 'varchar', 6)->default(null)->comment("6位验证码"),
            $table->field('codeLive', 'int', 11)->default(null)->comment("验证时间"),
            $table->field('password', 'varchar', 60)->default(null)->comment("密码"),
            $table->field('avatar', 'bigint', 20)->default(0)->comment("头像ID"),
            $table->field('signupIp', 'varchar', 32)->comment("注册IP"),
            $table->field('signupTime', 'int', 11)->comment("注册时间"),
            $table->field('status', 'tinyint', 1)->key()->default(self::STATUS_ACTIVE)->comment("用户状态")
        );
    }

    protected function _inputMobileField($mobile)
    {
        $mobile=trim($mobile);
        if (!preg_match(self::MOBILE_PREG, $mobile)) {
            throw new UserException('invalid user mobile', UserException::MOBILE_FORMAT_ERROR);
        }
        return $mobile;
    }

    protected function _inputNameField($name)
    {
        $name=trim($name);
        if (!preg_match(self::NAME_PREG, $name)) {
            throw new UserException('invalid user name', UserException::NAME_FORMAT_ERROR);
        }
        return $name;
    }

    protected function _inputEmailField($email)
    {
        $email=trim($email);
        if (!preg_match(self::EMAIL_PREG, $email)) {
            throw new UserException('invalid user email', UserException::EMAIL_FORMAT_ERROR);
        }
        return $email;
    }

    /**
     * 通过用户名获取用户
     *
     * @param string $name
     * @return array|null
     */
    public function getByName(string $name):?array
    {
        return $this->select('*', 'LOWER(name)=LOWER(:name)', ['name'=>$name])->fetch();
    }

    /**
     * 通过用户邮箱获取用户
     *
     * @param string $email
     * @return array|null
     */
    public function getByEmail(string $email):?array
    {
        return $this->select('*', 'LOWER(email)=LOWER(:email)', ['email'=>$email])->fetch();
    }

    /**
     * 通过手机号获取用户
     *
     * @param string $mobile
     * @return array|null
     */
    public function getByMobile(string $mobile):?array
    {
        return $this->select('*', ['mobile'=>$mobile])->fetch();
    }

    public function getByAccount(string $account):?array
    {
        if (preg_match(self::EMAIL_PREG, $account)) {
            return $this->getByEmail($account);
        } elseif (preg_match(self::MOBILE_PREG, $account)) {
            return $this->getByMobile($account);
        } else {
            return $this->getByName($account);
        }
    }

    public function changePassword(int $userid, string $password)
    {
        return $this->updateByPrimaryKey($userid, [
            'password'=>password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
    
    public function changePasswordByEmail(string $email, string $password)
    {
        return $this->update([
            'password'=>password_hash($password, PASSWORD_DEFAULT)
        ], ['email'=>$email]);
    }
    
    public function changePasswordByMobile(string $mobile, string $password)
    {
        return $this->update([
            'password'=>password_hash($password, PASSWORD_DEFAULT)
        ], ['mobile'=>$mobile]);
    }

    public function checkPassword(int $userid, string $password)
    {
        if ($user=$this->setFields(['password'])->getByPrimaryKey($userid)) {
            if (password_verify($password, $user['password'])) {
                return true;
            }
        }
        return false;
    }
}
