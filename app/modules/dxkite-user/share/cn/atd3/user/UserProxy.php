<?php
namespace cn\atd3\user;

use cn\atd3\visitor\Context;
use cn\atd3\visitor\verify\Image;
use cn\atd3\proxy\ProxyObject;
use cn\atd3\visitor\exception\PermissionExcepiton;

class UserProxy extends ProxyObject
{
    const EMAIL_PREG='/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
    /**
     * 错误代码
     */
    const INVALID_CODE=-1;
    const INVALID_ACCOUNT=-2;
    const INVALID_PASSWORD=-3;
    const ACTIVE=1;
    const FREEZE=0;
    const EXISTS_USER=-4;
    const EXISTS_EMAIL=-5;
    const MAINCODE_NAME='action_man';

    const SIGN_IN=1;
    const SIGN_UP=2;

    private $image;

    public function __construct()
    {
        parent::__construct();
        $this->image=new Image($this->context, self::MAINCODE_NAME);
    }
    
    public function signin(string $account, string $password, bool $remember=false, string $code=null)
    {
        if (self::getNeedSignCode(self::SIGN_IN)) {
            if (is_null($code)) {
                throw new UserException(__('you need enter image code'), UserException::NEED_CODE);
            } else {
                if (!self::checkCode($code)) {
                    return UserProxy::INVALID_CODE;
                }
            }
        }
    
        if (preg_match(UserProxy::EMAIL_PREG, $account)) {
            $id=Manager::getIdByEmail($account);
        } else {
            $id=Manager::getIdByName($account);
        }
    
        if ($id) {
            if (Manager::checkPassword($id, $password)) {
                $this->context->setSession('signFaildTimes', 0);
                (new User)->sign($id, $remember);
                return $id;
            } else {
                $get=$this->context->getSession('signFaildTimes', 0);
                $this->context->setSession('signFaildTimes', $get+1);
                if ($get>=conf('system.user_faildtimes', 3)) {
                    self::setNeedSignCode(self::SIGN_IN);
                }
                return UserProxy::INVALID_PASSWORD;
            }
        } else {
            return UserProxy::INVALID_ACCOUNT;
        }
        return $id;
    }

    /**
     * 注册用户
     * @open true
     * @param string $user
     * @param string $email
     * @param string $password
     * @param string $code
     * @return void
     */
    public function signup(string $user, string $email, string $password, string $code=null)
    {
        if (self::getNeedSignCode(self::SIGN_UP)) {
            if (is_null($code)) {
                throw new UserException(__('you need enter image code'), UserProxy::NEED_CODE);
            } else {
                if (!self::checkCode($code)) {
                    return UserProxy::INVALID_CODE;
                }
            }
        }
        $token=md5($user.microtime());
        $token_expire=time()+conf('system.user_expire', 86400);
        $id=Manager::add($user, $email, $password, $token, $token_expire);
        // 注册成功
        if ($id>0) {
            self::setNeedSignCode(self::SIGN_UP);
            $visitor->refresh($id, $token);
            $this->context->cookieVisitor($visitor)->expire($token_expire)->session()->set();
        }
        return $id;
    }

    public function signout()
    {
        $userId=$this->context->getVisitor()->getId();
        Manager::refershToken($userId, '', 0);
        $guest=new User;
        $this->context->setVisitor($guest);
        $this->context->cookieVisitor($guest);
        return $userId;
    }

    public function getInfo()
    {
        return Manager::getUserInfoById($this->context->getVisitor()->getId());
    }

    /**
     * 模拟其他用户
     * 
     * @?acl admin:user.simulate
     * @paramSource json,get
     * @param integer $id
     * @return void
     */
    public function simulate(int $id=0)
    {
        if ($id==0) {
            cookie()->unset(User::simulateUserToken);
        } else {
            cookie()->unset(User::simulateUserToken);
            if ($this->hasPermission('admin:user.simulate')) {
                cookie()->set(User::simulateUserToken, $id);
            } else {
                throw new PermissionExcepiton(__('permission deny: need permission [admin:user.simulate]'), 1);
            }
        }
        return $this->getContext()->getVisitor()->getId();
    }

    public function ids2name(array $ids)
    {
        return Manager::ids2name($ids);
    }
    
    public function id2name(int $id)
    {
        return self::ids2name([$id])[$id]??null;
    }

    /**
     * 获取是否需要验证验证码
     * 
     * @param int $type Signin=1,Signup=2
     * @return void
     */
    public function getNeedSignCode(int $type)
    {
        if ($type==self::SIGN_IN) {
            return $this->context->getSession('needSigninCode', false);
        }
        return $this->context->getSession('needSignupCode', false);
    }

    /**
     * 设置是否需要验证验证码
     * 
     * @param int $type Signin=1,Signup=2
     * @return void
     */
    protected function setNeedSignCode(int $type)
    {
        if ($type==self::SIGN_IN) {
            $this->context->setSession('needSigninCode', true);
        } else {
            $this->context->setSession('needSignupCode', true);
        }
    }
    
    public function getSignFaildTimes()
    {
        return $this->context->getSession('signFaildTimes', 0);
    }

    // TODO 获取头像
    public function getAvatar(int $id)
    {
        return false;
    }
    
    // TODO 设置头像
    public function setAvatar($file)
    {
        return false;
    }

    /**
     * 验证验证码
     *
     * @param string $code
     * @return void
     */
    protected function checkCode(string $code)
    {
        return $this->image->checkCode($code);
    }

    /**
     * 生成验证码图片
     * @binary png
     * @return void
     */
    public function displayImage()
    {
        return $this->image->display();
    }
}
