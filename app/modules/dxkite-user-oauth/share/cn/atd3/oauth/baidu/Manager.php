<?php
namespace cn\atd3\oauth\baidu;

use suda\template\Manager as TemplateManger;
use cn\atd3\proxy\ProxyObject;
use cn\atd3\user\User;
use cn\atd3\user\dao\UserDAO;

class Manager extends ProxyObject
{
    protected $baidu=null;
    protected $user=null;

    public function __construct()
    {
        parent::__construct();
        $this->baidu = new BaiduTable;
        $this->user=new UserDAO;
    }

    public static function adminItem($template)
    {
        TemplateManger::include('user-oauth:baidu/setting', $template)->render();
    }

    public function authedBaidu(string $code)
    {
        $visitor=$this->context->getVisitor();
        $info=$this->getAuthedInfo($code);
        if (isset($info['error'])) {
            return false; // throw new Exception($info['error_description']);
        }
        $user=new Baidu($info['access_token']);
        $userInfo=$user->getLoggedInUser();
        if ($userInfo && isset($userInfo['uid'])) {
            $exist=$this->baidu->select(['id','user','uid'], ['uid'=>$userInfo['uid']])->fetch();
            if ($exist) {
                if ($visitor->isGuest()) {
                    // signin
                    $visitor->sign($exist['user'], true);
                }
                $this->baidu->update([
                    'access_token'=>$info['access_token'],
                    'refresh_token'=>$info['refresh_token'],
                    'scope'=>$info['scope'],
                    'expires_in'=> time() + $info['expires_in'],
                ], ['uid'=>$userInfo['uid']]);
                return true;
            } else {
                $data=[
                    'uid'=>$userInfo['uid'],
                    'uname'=>$userInfo['uname'],
                    'access_token'=>$info['access_token'],
                    'refresh_token'=>$info['refresh_token'],
                    'scope'=>$info['scope'],
                    'expires_in'=> time() + $info['expires_in'],
                ];
                if ($visitor->isGuest()) {
                    $this->baidu->insert($data);
                    return $userInfo['uid'];
                } else {
                    $data['user']=$visitor->getId();
                    $this->baidu->insert($data);
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    public function checkNameExist(string $name) 
    {
        return $this->user->checkNameExists($name);
    }
    
    public function checkEmailExists(string $name) 
    {
        return $this->user->checkEmailExists($name);
    }
    
    public function create(int $uid,string $email,string $name) {
        if (self::checkNameExists($name)) {
            return UserDAO::EXISTS_USER;
        }
        if (self::checkEmailExists($email)) {
            return UserDAO::EXISTS_EMAIL;
        }
        return $this->insert([
            'name'=>$name,
            'email'=>$email,
            'signup_time'=>time(),
            'signup_ip'=>request()->ip(),
            'status'=>UserDAO::ACTIVE,
            'valid_token'=>'',
            'valid_expire'=>'',
        ]);
    }

    public static function getAuthUrl()
    {
        $redirectUrl=u('user-oauth:baidu-callback');
        $queryStrArr=[
            'scope'=>setting('baidu-scope', 'basic'),
            'client_id'=>setting('baidu-client-id'),
            'redirect_uri'=>$redirectUrl,
        ];
        $url=setting('baidu-auth-url', 'http://openapi.baidu.com/oauth/2.0/authorize?response_type=code&display=popup');
        return self::urlAppendQuery($url, $queryStrArr);
    }

    protected static function getAuthedInfo(string $code)
    {
        $redirectUrl=u('user-oauth:baidu-callback');
        $queryStrArr=[
            'grant_type'=>setting('baidu-grant-type', 'authorization_code'),
            'code'=>$code,
            'client_id'=>setting('baidu-client-id'),
            'client_secret'=>setting('baidu-client-secret'),
            'redirect_uri'=>$redirectUrl,
        ];
        $baiduUrl=setting('baidu-access-token-url', 'https://openapi.baidu.com/oauth/2.0/token');
        $url=self::urlAppendQuery($baiduUrl, $queryStrArr);
        $json=self::curl($url);
        debug()->debug('access-json', $json);
        return json_decode($json, true);
    }

    protected static function urlAppendQuery(string $url, array $queryStrArr)
    {
        $parsed=parse_url($url);
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $query);
            $queryStrArr=array_merge($query, $queryStrArr);
        }
        $queryStr=http_build_query($queryStrArr);
        $scheme=$parsed['scheme']??'http';
        $host=$parsed['host']??'localhost';
        $port=isset($parsed['port'])  && $parsed['port']!=80?':'.$parsed['port']:'';
        $path=$parsed['path']??'/';
        return $scheme.'://'.$host.$port.$path.'?'.$queryStr;
    }

    public static function curl(string $url)
    {
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $file=curl_exec($ch);
        curl_close($ch);
        return $file;
    }
}
