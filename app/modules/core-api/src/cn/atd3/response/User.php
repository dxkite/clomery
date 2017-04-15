<?php
namespace cn\atd3\response;

use cn\atd3\Session;
use cn\atd3\ApiException,Token;
use suda\tool\Value;

/**
* visit url /api/user[/{action}] as all method to run this class.
* you call use u('user_api',Array) to create path.
* @template: default:api/user.tpl.html
* @name: user_api
* @url: /api/user[/{action}]
* @param: action:string,
*/
class User extends \cn\atd3\ApiAction
{
    // 记住登陆时间30天
    /**
    * 注册是否需要验证码
    */
    public function actionSignupCodeIfy()
    {
        return Session::get('signupcode', false);
    }

    /**
    * 登陆是否需要验证码
    */
    public function actionSigninCodeIfy()
    {
        return Session::get('signincode', false);
    }

    /**
    * 验证邮箱是否存在
    */
    public function actionCheckEmailExist(string $email)
    {
        return $this->uc->checkEmailExist($email);
    }

    /**
    * 验证用户名是否存在
    */
    public function actionCheckNameExist(string $name)
    {
        return $this->uc->checkNameExist($name);
    }


    /**
    * 列出用户
    * @auths: admin
    */
    public function actionList(int $page=1, int $count=10)
    {
        return $this->uc->getUser($page, $count);
    }

    /**
    * 获取用户数量
    * @auths: admin
    */
    public function actionCount()
    {
        return  $this->uc->countUser();
    }

    /**
    *  ID转换成用户名
    * @auths
    */
    public function actionId2name(array $uids)
    {
        return $this->uc->id2name($uids);
    }

    /**
    * 获取指定ID的用户公开信息
    * @auths
    */
    public function actionPublicInfo(array $uids)
    {
        return $this->uc->getUserPublicInfoByIds($uids);
    }

    /**
    * 获取当前用户登陆ID
    * @auths
    */
    public function actionId()
    {
        return \cn\atd3\User::getUserId();
    }

    /**
    * 用户注册
    */
    public function actionSignup(string $name, string $email, string $passwd, string $code=null)
    {
        // 用户名格式错误
        if (!$this->uc->checkNameFormat($name)) {
            throw new ApiException('nameFormatError', _T('用户名格式错误') );
        }
        // 邮箱格式错误
        if (!$this->uc->checkEmailFormat($email)) {
            throw new ApiException('emailFormatError', _T('邮箱格式错误') );
        }
        // 需要验证码却未设置
        if (is_null($code) &&Session::get('signupcode', false)) {
            throw new ApiException('lackCodeError', _T('需要验证码') );
        }
        
        // 邮箱验证码
        $emailcode= rand(1000, 9999);
        // ip首次注册不需要验证码
        if (!Session::get('signupcode', false)) {
            $id=$this->uc->addUser($name, $passwd, $email, 0, $this->request->ip());
            if ($id) {
                Session::set('signupcode', true);
            }
            
            $get=$this->uc->createToken($id, $this->client, $this->token, $this->request->ip(), $emailcode);
            \cn\atd3\Mail::sendCheckMail($email, $emailcode);
            Token::set('user', $token= base64_encode($get['id'].'.'.$get['token']));
            return ['uid'=>$id,'token'=>$token,'email_token'=>$get['token']];
        }
        // 二次注册需要验证码
        elseif (Session::get('signupcode', false)&& \cn\atd3\VerifyImage::checkCode($code)) {
            $id=$this->uc->addUser($name, $passwd, $email, 0, $this->request->ip());
            $get=$this->uc->createToken($id, $this->client, $this->token, $this->request->ip(), $emailcode);
            Token::set('user', $token= base64_encode($get['id'].'.'.$get['token']));
            \cn\atd3\Mail::sendCheckMail($email, $emailcode);
            return ['uid'=>$id,'token'=>$token,'email_token'=>$get['token']];
        } else {
            Session::set('signupcode', true);
            throw new ApiException('codeError', _T('验证码错误') );
        }
    }

    /**
    * 用户注册
    */
    public function actionSignupWithoutPasswd(string $name, string $email,string $code=null){
        // 用户名格式错误
        if (!$this->uc->checkNameFormat($name)) {
            throw new ApiException('nameFormatError', _T('用户名格式错误') );
        }
        // 邮箱格式错误
        if (!$this->uc->checkEmailFormat($email)) {
            throw new ApiException('emailFormatError', _T('邮箱格式错误') );
        }
        // 需要验证码却未设置
        if (is_null($code) &&Session::get('signupcode', false)) {
            throw new ApiException('lackCodeError', _T('需要验证码') );
        }
        
        // 邮箱验证码
        $passwd= md5(microtime(true));
        // ip首次注册不需要验证码
        if (!Session::get('signupcode', false)) {
            $id=$this->uc->addUser($name,$passwd,$email, 0, $this->request->ip());
            if ($id) {
                Session::set('signupcode', true);
            }
            $get=$this->uc->createToken($id, $this->client, $this->token, $this->request->ip(), $passwd);
            \cn\atd3\Mail::sendCheckMail($email, $passwd);
            Token::set('user', $token= base64_encode($get['id'].'.'.$get['token']));
            return ['uid'=>$id,'token'=>$token];
        }
        // 二次注册需要验证码
        elseif (Session::get('signupcode', false)&& \cn\atd3\VerifyImage::checkCode($code)) {
            $id=$this->uc->addUser($name, $passwd, $email, 0, $this->request->ip());
            $get=$this->uc->createToken($id, $this->client, $this->token, $this->request->ip(), $passwd);
            Token::set('user', $token= base64_encode($get['id'].'.'.$get['token']));
            \cn\atd3\Mail::sendCheckMail($email, $passwd);
            return ['uid'=>$id,'token'=>$token];
        } else {
            Session::set('signupcode', true);
            throw new ApiException('codeError', _T('验证码错误') );
        }
    }

    /**
    * 修改密码
    * @auths
    */
    public function actionChangePasswd(string $oldpasswd, string $passwd,string $code=null){
        $uid=\cn\atd3\User::getUserId();
        if (!Session::get('changepasswdcode', false)) {
            Session::set('changepasswdcode', false);
            $this->uc->deleteToken($this->client, $this->token);
            return $this->uc->changePassword($uid,$oldpasswd,$passwd);
        }
        elseif (Session::get('changepasswdcode', false) && \cn\atd3\VerifyImage::checkCode($code)) {
            Session::set('changepasswdcode', false);
            $this->uc->deleteToken($this->client, $this->token);
            return $this->uc->changePassword($uid,$oldpasswd,$passwd);
        } else {
            Session::set('changepasswdfaild', Session::get('changepasswdfaild') +1);
            if (Session::get('changepasswdfaild')>3){
                Session::set('changepasswdcode', true);
            }
            throw new ApiException('codeError', _T('验证码错误') );
        }
    }
    /**
    * 设置用户头像
    * @auths
    */
    public function actionAvatar(int $id)
    {
        $uid=\cn\atd3\User::getUserId();
        return $this->uc->setUserAvatar($uid, $id);
    }

    /**
    * 用户登陆
    */
    public function actionSignin(string $name, string $passwd, string $code=null/*, bool $remember=false*/)
    {
        $remember=false;
        // 验证用户名格式
        if (!$this->uc->checkNameFormat($name)) {
            throw new ApiException('nameFormatError', _T('用户名格式错误') );
        }
        
        // 验证码检查
        if (Session::get('signincode', false)) {
            if (is_null($code)) {
                _D()->d('lackCodeError');
                throw new ApiException('lackCodeError', _T('需要验证码') );
            }
            // 验证验证码
            elseif (!\cn\atd3\VerifyImage::checkCode($code)) {
                throw new ApiException('codeError',_T('验证码错误') );
            }
        }

        // 检测失败次数是否达到上限
        // 刷新验证码需求
        if (Session::get('faild_times', 0)>3) {
            Session::set('signincode', true);
        }

        // 检测密码
        if ($id=$this->uc->checkPassword($name, $passwd)) {
            // 登陆成功
            $get=$this->uc->createToken($id, $this->client, $this->token, $this->request->ip(),$remember?‪2592000‬:0);
            Token::set('user', base64_encode($get['id'].'.'.$get['token'].'.'.$get['value']), $remember?‪2592000‬:3600)->session(!$remember)->httpOnly();
            // 清空验登陆失败次数
            Session::set('faild_times', 0);
            // 取消验证码需求
            Session::set('signincode', false);
            return true;
        } else {
            // 增加登陆失败次数
            Session::set('faild_times', Session::get('faild_times', 0)+1);
            return false;
        }
    }

    /**
    * 获取登陆的用户信息
    * @auths
    */
    public function actionInfo()
    {
        if (Token::has('user')) {
            $token=base64_decode(Token::get('user'));
            if (preg_match('/^(\d+)[.]([a-zA-Z0-9]{32})(?:[.]([a-zA-Z0-9]{32}))?$/', $token, $match)) {
                // var_dump($match);
                if ($uid=$this->uc->tokenAvailable(intval($match[1]), $match[2])) {
                    return ($this->uc->getUserById([intval($uid['user'])])[intval($uid['user'])]);
                }
            }
        }
        return false;
    }

    /**
    * 退出登陆
    * @auths
    */
    public function actionSignout()
    {
        return $this->uc->deleteToken($this->client, $this->token);
    }

    /**
    * 心跳包
    * @auths
    */
    public function actionBeat()
    {
        $token=base64_decode(Token::get('user'));
        if (preg_match('/^(\d+)[.]([a-zA-Z0-9]{32})(?:[.]([a-zA-Z0-9]{32}))?$/', $token, $match)) {
            if (!isset($match[3])) {
                throw new ApiException('lackTokenString', _T('心跳包不完整'), $match);
            }
            if ($get=$this->uc->refreshToken(intval($match[1]), $this->client, $this->token, $match[3])) {
                Token::set('user', $token= base64_encode($get['id'].'.'.$get['token'].'.'.$get['value']));
                return  true;
            } else {
                throw new ApiException('refreshTokenError', _T('心跳包更新失败') , $match);
            }
        }
        throw new ApiException('unknownError', _T('心跳包未知错误') , $token);
    }

   /**
    * 验证用户邮箱
    * @auths
    */
    public function actionCheckUserEmail(string $token, string $value)
    {
        $token=base64_decode($token);
        if (preg_match('/^(\d+)[.]([a-zA-Z0-9]{32})(?:[.]([a-zA-Z0-9]{32}))?$/', $token, $match)) {
            if ($uid=$this->uc->verifyTokenValue(intval($match[1]), $match[2], $value)) {
                return $this->uc->setEmailAvailable([intval($uid)]);
            }
        }
        return false;
    }

    /**
    * 设置用户邮箱
    * @auths
    */
    public function actionSetUserEmail(string $email)
    {
        if ($email && !$this->uc->checkEmailFormat($email)) {
            throw new ApiException('emailFormatError', _T('邮箱格式错误') );
        }
        if ($user=self::getUserInfo()) {
            $user=array_shift($user);
            $get=$this->uc->createToken($user['id'], $this->client, $this->token, $this->request->ip(), 'Code');
            Token::set('user', $token= base64_encode($get['id'].'.'.$get['token']));
            return ['edit'=>$this->uc->editUser($user['id'], '', '',  $email, 0, 0, '', ''),'token'=>$token];
        }
        return false;
    }

    /**
    * 设置用户头像
    * @auths
    */
    public function actionSetUserAvatar(int  $avatar)
    {
        if ($email && !$this->uc->checkEmailFormat($email)) {
            throw new ApiException('emailFormatError', _T('邮箱格式错误') );
        }
        if ($user=self::getUserInfo()) {
            $user=array_shift($user);
            $get=$this->uc->createToken($user['id'], $this->client, $this->token, $this->request->ip(), 'Code');
            Token::set('user', $token= base64_encode($get['id'].'.'.$get['token']));
            return ['edit'=>$this->uc->editUser($user['id'], '', '', '', 0, 0, '', $avatar),'token'=>$token];
        }
        return false;
    }
}
