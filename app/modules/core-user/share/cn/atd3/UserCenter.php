<?php
namespace cn\atd3;

use suda\core\Query;

// 用户中心
// 用户中心适配器：
// 用于适配其他用户中心的操作（exp:dz）
// TODO  删除某权限
// TODO  验证Token的时候验证IP
// TODO  隐式心跳

class UserCenter
{
    const REG_EMAIL='/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
    const REG_NAME='/^[\w\x{4e00}-\x{9aff}]{4,13}$/u';
    const CLIENT_ACTIVE=1;//可活动的
    const CLIENT_FREEZE=0;//禁用的
     //-------------------
    //   用户基本操作
    //-------------------
    // 数据检验

    public static function checkNameExist(string $name):bool
    {
        return Query::where('user', 'id', 'LOWER(name) = LOWER(:name)', ['name'=>$name])->fetch()?true:false;
    }

    public static function checkEmailExist(string $email):bool
    {
        return Query::where('user', 'id', 'LOWER(email) = LOWER(:email)', ['email'=>$email])->fetch()?true:false;
    }
    // 数据格式效验
    public static function checkNameFormat(string $name):bool
    {
        return preg_match(self::REG_NAME, $name);
    }


    public static function checkEmailFormat(string $email):bool
    {
        return preg_match(self::REG_EMAIL, $email);
    }

    // 验证密码
    public static function checkPassword(string $name, string $password)
    {
        if ($fetch=Query::where('user', ['password', 'id'], ['name'=>$name])->fetch()) {
            if (password_verify($password, $fetch['password'])) {
                return $fetch['id'];
            }
        }
        return false;
    }

    // 修改密码
    public static function changePassword(int $id, string $oldpasswd,string $password):bool
    {
        if ($fetch=Query::where('user', ['password'], ['id'=>$id])->fetch()) {
            if (password_verify($oldpasswd, $fetch['password'])) {
                 return Query::update('user', ['password'=>password_hash($password, PASSWORD_DEFAULT)], ['id'=>$id]);
            }
        }
        return false;
    }

    public static function checkEmailavailable(int $uid):bool
    {
        return Query::where('user', 'id', ['id'=>$uid, 'available'=>true])->fetch()?true:false;
    }
    
    public static function setEmailAvailable(array $uid, bool $available=true):bool
    {
        return Query::update('user', ['available'=>$available], ['id'=>$uid]);
    }

    // 基本操作
    public static function addUser(string $name, string $password, string  $email, int $group, string $ip, int $avatar=0):int
    {
        $insert= [
            'name'=>$name,
            'password'=> password_hash($password, PASSWORD_DEFAULT),
            'group'=>$group,
            'available'=>false,
            'email'=>$email,
            'ip'=>$ip,
            ];
        if ($avatar) {
            $insert['avatar']=$avatar;
        }
        return Query::insert('user', $insert);
    }

    public static function editUser(int $uid, string $name, string $password, string $email,int $available, int $group,int $avatar):bool
    {
        $sets=[];
        if ($name) {
            $sets['name']=$name;
        }
        if ($password) {
            $sets['password']=password_hash($password, PASSWORD_DEFAULT);
        }
        if ($email) {
            $sets['email']=$email;
        }
        if ($group) {
            $sets['group']=$group;
        }
        if ($avatar) {
            $sets['avatar']=$avatar;
        }
        if ($available){
            $sets['available']=$available;
        }
        return Query::update('user', $sets, ['id'=>$uid]);
    }
    public static function setUserAvatar(int $uid,int $avatar):bool
    {
        return Query::update('user', ['avatar'=>$avatar], ['id'=>$uid]);
    }

    public static function deleteUser(array $uidarray):bool
    {
        return Query::delete('user', ['id'=>$uidarray]);
    }
    public static function countUser():int
    {
        return Query::count('user');
    }
    public static function getUser(int $page, int $counts):array
    {
        return ($fetch=Query::where('user', ['id', 'name', 'email', 'available', 'avatar', 'ip'], '1', [], [$page, $counts])->fetchAll())?$fetch:[];
    }

    public static function getUserById(array $uid):array
    {
        if ($fetch=Query::where('user', ['id', 'name', 'email', 'available', 'avatar', 'ip'], ['id'=>$uid])->fetchAll()) {
            $data=[];
            foreach ($fetch as $item) {
                $data[$item['id']]=$item;
            }
            return $data;
        }
        return  [];
    }
    
    public static function getUserPublicInfoByIds(array $uid):array
    {
        if ($fetch=Query::where('user', ['id', 'name', 'email', 'avatar' ], ['id'=>$uid])->fetchAll()) {
            $data=[];
            foreach ($fetch as $item) {
                $data[$item['id']]=$item;
            }
            return $data;
        }
        return  [];
    }

    public static function getUserByName(array $names):array
    {
        if ($fetch=Query::where('user', ['id', 'name', 'email', 'available', 'avatar', 'ip'], ['name'=>$names])->fetchAll()) {
            $data=[];
            foreach ($fetch as $item) {
                $data[$item['name']]=$item;
            }
            return $data;
        }
        return  [];
    }

    public static function getUserByEmail(array $emails):array
    {
        if ($fetch=Query::where('user', ['id', 'name', 'email', 'available', 'avatar', 'ip'], ['email'=>$emails])->fetchAll()) {
            $data=[];
            foreach ($fetch as $item) {
                $data[$item['email']]=$item;
            }
            return $data;
        }
        return  [];
    }

    // 数据转换
    public static function id2name(array $ids)
    {
        if ($fetch=Query::where('user', ['id', 'name'], ['id'=>$ids])->fetchAll()) {
            $data=[];
            foreach ($fetch as $item) {
                $data[$item['id']]=$item['name'];
            }
            return $data;
        }
        return  false;
    }

    public static function name2id(array $names):array
    {
        if ($fetch=Query::where('user', ['id', 'name'],  ['names'=>$names])->fetchAll()) {
            $data=[];
            foreach ($fetch as $item) {
                $data[$item['name']]=$item['id'];
            }
            return $data;
        }
        return  false;
    }

    public static function email2id(array $email):array
    {
        if ($fetch=Query::where('user', [ 'id', 'email'], ['email'=>$email])->fetchAll()) {
            $data=[];
            foreach ($fetch as $item) {
                $data[$item['email']]=$item['id'];
            }
            return $data;
        }
        return  false;
    }

    public static function id2email(array $ids):array
    {
        if ($fetch=Query::where('user', ['id', 'email'], ['id'=>$ids])->fetchAll()) {
            $data=[];
            foreach ($fetch as $item) {
                $data[$item['id']]=$item['email'];
            }
            return $data;
        }
        return  false;
    }

    // 权限操作
    public static function getUserPermission(int $uid):array
    {
        // 获取权限
        if ($fetch=Query::select('user_group', 'auths', ' JOIN `#{user}` ON `#{user}`.`id` = :id  WHERE `user` = :id  or `#{user_group}`.`id` =`#{user}`.`group` LIMIT 1;', ['id'=>$uid])->fetch()) {
            return ($auths=json_decode($fetch['auths']))?$auths:[];
        }
        return [];
    }

    public static function setUserPermission(int $id, $permissions):bool
    {
        $permissions=is_array($permissions)?$permissions:[$permissions];
        try {
            Query::begin(); 
            $older=self::getUserPermission($id);
            if ($older===false) {
                $older=[];
            }
            $diff=array_diff($older, $permissions);
            $permissions=array_merge($diff, $permissions);
            if ($fetch=Query::where('user_group', 'id', ['user'=>$id])->fetch()) {
                Query::update('user_group', ['auths'=>json_encode($permissions)], ['id'=>$fetch['id']]);
            } else {
                Query::insert('user_group', ['auths'=>json_encode($permissions), 'user'=>$id, 'name'=>'User:'.$id]);
            }
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return true;
    }

    //-------------------
    //   分组操作
    //-------------------
    public static function getGroupPermission(int $group):array
    {
        // 获取权限
        if ($fetch=Query::select('user_group', 'auths', ['id'=>$group])->fetchAll()) {
            return ($auths=json_decode($fetch['auths']))?$auths:[];
        }
        return [];
    }

    public static function setGroupPermission(array $groups, array $permissions):bool
    {
        try {
            Query::begin();
            $older=self::getGroupPermission($id);
            if ($older===false) {
                $older=[];
            }
            $permissions=array_merge($older, $permissions);
            Query::update('user_group', ['auths'=>json_encode($permissions)], ['id'=>$groups]);
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return true;
    }

    public static function addGroup(string $gname, array $Permission):int
    {
        return Query::insert('user_group', ['name'=>$gname, 'auths'=>json_encode($permissions)]);
    }

    public static function deleteGroup(array $groups):bool
    {
        return Query::delete('user_group', ['id'=>$groups])->fetch();
    }

    public static function gid2name(array $ids):array
    {
        return ($fetch=Query::select('user_group', ['id', 'name'], ['id'=>$ids])->fetchAll())?$fetch:[];
    }

    public static function gname2id(array $names):int
    {
        return ($fetch=Query::select('user_group', ['id', 'name'], ['name'=>$names])->fetchAll())?$fetch:[];
    }
    public static function getGroupByID(array $ids):array
    {
        return ($fetch=Query::select('user_group', ['id', 'name', 'auths'], ['id'=>$ids])->fetchAll())?$fetch:[];
    }
    public static function getGroup(int $page, int $count):array
    {
        return ($fetch=Query::select('user_group', ['id', 'name', 'auths'], ['user'=>''])->fetchAll())?$fetch:[];
    }


    public static function addClient(string  $name, string $description, array $auths=[], int $beat=60, int $alive=3600, int $state=1)
    {
        $token=md5(microtime(true));
        $id=Query::insert('user_client', ['name'=>$name, 'description'=>$description, 'auths'=>json_encode($auths), 'time'=>time(), 'beat'=>$beat, 'alive'=>$alive, 'token'=>$token, 'state'=>$state]);
        return ['id'=>$id,'token'=>$token];
    }

    public static function setClientState(int $id, int $state)
    {
        return Query::update('user_client', ['state'=>$state], ['id'=>$id]);
    }

    public static function getClientById(int $id)
    {
        return ($get=Query::where('user_client', '*', ['id'=>$id])->fetch())?$get:false;
    }

    public static function listClient(int $state=null, int $page=1, int $per_page=10)
    {
        if (is_null($state)) {
            return Query::where('user_client')->fetchAll();
        }
        return Query::where('user_client', '*', ['state'=>$state], [$page, $per_page])->fetchAll();
    }

    public static function checkClient(int $id, string $token)
    {
        return Query::where('user_client', ['id', 'alive', 'beat'], ['id'=>$id, 'token'=>$token, 'state'=>self::CLIENT_ACTIVE])->fetch();
    }


    // 生成令牌
    protected static function generate(int $user, string $tokenname)
    {
        static $mis='5246-687261-5852-6C';
        return md5('DXCore-'.$user.'-'.microtime(true).'-'.$mis.'-'.$tokenname);
    }


    public static function createToken(int $user, int $client, string $client_token, string $ip, string $value=null,int $settime=0)
    {
        // 客户端可用
        if ($get=self::checkClient($client, $client_token)) {
            // 存在同名Token则更新
            if ($fetch=Query::where('user_token', ['id', 'value'], '`user`=:user AND `client`=:client AND `expire` > UNIX_TIMESTAMP()  AND LENGTH(`value`) = '. (strlen($value)?:'32') , ['user'=>$user, 'client'=>$client])->fetch()) {
                return self::refreshToken($fetch['id'], $client, $client_token, $fetch['value']);
            } else { // 创建新Token
                $verify=self::generate($user, $client);
                if (is_null($value)) {
                    $value=self::generate($user, $verify);
                }
                $time=time();
                $token=Query::insert('user_token', ['user'=>$user, 'token'=>$verify, 'time'=>$time, 'ip'=>$ip, 'client'=>$client, 'expire'=>$time + ($settime?:$get['beat']), 'value'=>$value]);
                return ['id'=>$token,'token'=>$verify,'time'=>$time,'value'=>$value];
            }
        }
        return false;
    }
    // 刷新过期时间
    public static function refreshToken(int $id, int $client, string $client_token, string $value, string $refresh=null,int $settime=0)
    {
        if ($get=self::checkClient($client, $client_token)) {
            $new =self::generate($id, $value);
            if (is_null($refresh)) {
                $refresh=self::generate($id, $new);
            }
            if (Query::update('user_token', 'expire = :time , token=:new_token,value=:refresh', 'id=:id AND UNIX_TIMESTAMP() < `time` + :alive AND value = :value ', ['id'=>$id, 'value'=>$value, 'new_token'=>$new, 'refresh'=>$refresh, 'time'=>time() + $get['beat'], 'alive'=>$get['alive']])) {
                return  ['id'=>$id, 'token'=>$new, 'time'=>time() + ($settime?:$get['beat']) ,'value'=>$refresh];
            }
        }
        return false;
    }
    // 验证令牌值
    public static function verifyTokenValue(int $id, string $token, string $value)
    {
        return ($user=Query::where('user_token', 'user', '`id` =:id AND `expire` > UNIX_TIMESTAMP() AND LOWER(token) = LOWER(:token) AND `value` =:value', ['id'=>$id, 'token'=>$token, 'value'=>$value])->fetch())?$user['user']:false;
    }

    // 验证令牌是否过期
    public static function tokenAvailable(int $id, string $token)
    {
        return Query::where('user_token', 'user', 'id =:id AND `expire` > UNIX_TIMESTAMP() AND LOWER(token) = LOWER(:token) ', ['id'=>$id, 'token'=>$token ])->fetch();
    }

    // 删除令牌
    public static function deleteToken(int $id, string $token)
    {
        return Query::update('user_token', '`expire`=UNIX_TIMESTAMP()', ['id'=>$id, 'token'=>$token]);
    }
}
