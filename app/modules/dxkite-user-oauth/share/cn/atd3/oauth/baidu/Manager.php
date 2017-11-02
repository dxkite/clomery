<?php
namespace cn\atd3\oauth\baidu;

use suda\template\Manager as TemplateManger;
use cn\atd3\visitor\Visitor;

class Manager
{
    protected $baidu=null;
    protected $user=null;

    public function __construct()
    {
        $this->baidu=table('baidu_user');
        $this->user=table('user');
    }

    public static function adminItem($template)
    {
        TemplateManger::include('user-oauth:baidu/setting', $template)->render();
    }

    /**
     * 百度用户登陆
     *
     * @param Visitor $visitor
     * @param string $code
     * @return bool true|false|id 登陆成功|失败|创建UID
     */
    public function baiduSign(Visitor $visitor,string $code)
    {
        $info=$this->getAuthedInfo($code);
        if (isset($info['error'])) {
            return false; // throw new Exception($info['error_description']);
        }
        $user=new Baidu($info['access_token']);
        $userInfo=$user->getLoggedInUser();
        if ($userInfo && isset($userInfo['uid'])) {
            $exist=$this->baidu->select(['id','user','uid'], ['uid'=>$userInfo['uid']])->fetch();
            // 是否绑定百度
            if ($exist) {
                // 游客|绑定->登陆
                if ($visitor->isGuest()) {
                    $visitor->sign($exist['user'], true);
                }
                // 更新Token
                $this->baidu->update([
                    'access_token'=>$info['access_token'],
                    'refresh_token'=>$info['refresh_token'],
                    'scope'=>$info['scope'],
                    'expires_in'=> time() + $info['expires_in'],
                ], ['uid'=>$userInfo['uid']]);
                return true; // 登陆成功
            } else {
                
                $data=[
                    'uid'=>$userInfo['uid'],
                    'uname'=>$userInfo['uname'],
                    'portrait'=>$userInfo['portrait'],
                    'access_token'=>$info['access_token'],
                    'refresh_token'=>$info['refresh_token'],
                    'scope'=>$info['scope'],
                    'expires_in'=> time() + $info['expires_in'],
                ];

                // 游客|不存在的用户 -> 创建用户
                if ($visitor->isGuest()) {
                    // TODO: 添加头像处理
                    $newUser=[
                        'signup_time'=>time(),
                        'signup_ip'=>request()->ip(),
                        'status'=>$this->user::ACTIVE,
                    ];
                    // 不冲突则直接引用
                    if (!$this->checkNameExist($userInfo['uname'])) {
                        $newUser['name']=$userInfo['uname'];
                    }
                    // 创建新用户
                    $userId=$this->user->insert($newUser);
                    $data['user']=$userId;
                    // 绑定百度
                    $this->baidu->insert($data);
                    // 登陆用户
                    $visitor->sign($userId, true);
                    return $userId;
                } else {
                    // 登陆用户|绑定百度
                    $data['user']=$visitor->getId();
                    $this->baidu->insert($data);
                    return true; // 登陆成功
                }
            }
        } else {
            return false; // 登陆失败
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
    
    public static function getAuthUrl()
    {
        $redirectUrl=u('user-oauth:baidu-callback');
        $queryStrArr=[
            'scope'=>setting('baidu-scope', 'basic'),
            'client_id'=>setting('baidu-client-id'),
            'redirect_uri'=>$redirectUrl,
        ];
        $url=setting('baidu-auth-url', 'http://openapi.baidu.com/oauth/2.0/authorize?response_type=code&display=popup');
        return static::urlAppendQuery($url, $queryStrArr);
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
        $url=static::urlAppendQuery($baiduUrl, $queryStrArr);
        $json=static::curl($url);
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
