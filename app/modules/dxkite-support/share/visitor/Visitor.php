<?php
namespace dxkite\support\visitor;

use dxkite\support\table\visitor\RoleTable;
use dxkite\support\table\visitor\GrantTable;
use dxkite\support\table\visitor\SessionTable;

class Visitor implements \JsonSerializable
{
    const  simulateUserToken='__suid';

    protected $id;
    protected $token;
    protected $permission=null;
    protected $simulate=null;
    protected $expireTime = 0;

    const  MASK=0x19980602;

    public function __construct(int $user = 0)
    {
        $this->id = $user;
        if ($this->id == 0) {
            $this->permission =  new Permission;
            $this->token = md5(__DIR__.__CLASS__.PHP_VERSION.SUDA_VERSION);
        } else {
            $this->permission = self::loadPermission($user);
        }
    }

    public function getId()
    {
        return $this->isSimulateMode()?$this->simulate->id:$this->id;
    }
    
    public function isSimulateMode()
    {
        return !is_null($this->simulate);
    }
    
    public function simulateIfy()
    {
        $userId=intval(cookie()->get(self::simulateUserToken));
        if ($userId > 0 && $userId != $this->id) {
            return $this->simulateUser($userId);
        }
    }

    public function simulateUser(int $userId)
    {
        if ($this->hasPermission('visitor.simulate')) {
            $this->simulate= new Visitor($userId);
            Context::getInstance()->saveVisitor($this);
            cookie()->set(self::simulateUserToken, $userId)->set();
            return true;
        }
        return false;
    }

    public function clearSimulateMode()
    {
        $this->simulate = null;
    }

    // 设置登陆状态
    public function signin(int $id, int $expireTime=3600, bool  $remember=false)
    {
        $token =md5($id.microtime(true).$remember.$expireTime);
        $expireTime = $remember?conf('signin.long', 604800):conf('sigin.short', $expireTime);
        $fullExpireTime = $expireTime + time();
        return $this->signinWithToken($id, $token, $fullExpireTime, conf('sigin.beat', 3600));
    }

    // 设置登陆状态
    public function signinWithToken(int $id, string $token, int $fullExpireTime, int $beatTime = 3600)
    {
        // 过期时间
        // 刷新状态
        if ($session = (new SessionTable)->query('SELECT `id`,`expire`,`token` FROM #{@table@} WHERE grantee = :grantee and expire > :expire', [
                'grantee'=>$id,
                'expire'=>time(),
            ])->fetch()) {
            $this->beat($beatTime);
            $sessionId = $session['id'];
            $this->token =  $session['token'];
            $this->expireTime =$session['expire'];
        } else {
            $sessionId=(new SessionTable)->insert([
                'grantee'=>$id,
                'expire'=> $fullExpireTime,
                'ip'=>request()->ip(),
                'time'=>time(),
                'token'=> $token,
            ]);
            $this->expireTime = $fullExpireTime;
            $this->token = $token;
        }
        if ($sessionId) {
            $this->id = $id;
            $this->permission = self::loadPermission($id);
            Context::getInstance()->saveVisitor($this);
            hook()->exec('support:visitor::signin', [$this]);
        }
        return $this;
    }

    public function beat(int $expireTime=3600)
    {
        $expireTime = $this->expireTime;
        // 最后一分钟内可以刷新
        if ($expireTime && $expireTime - time()  < conf('session.beat', 60)) {
            if ($session = (new SessionTable)->query('SELECT id,expire FROM #{@table@} WHERE grantee = :grantee and expire > :expire', [
                'grantee'=> $this->id,
                'expire'=>time(),
            ])->fetch()) {
                $this->expireTime = $session['expire']+$expireTime;
                Context::getInstance()->saveVisitor($this);
                hook()->exec('support:visitor::beat', [$this]);
                return (new SessionTable)->updateByPrimaryKey($session['id'], ['expire'=>$session['expire']+$expireTime]);
            }
        }
        return false;
    }
    
    public function signout(?int $user=null)
    {
        if ($session = (new SessionTable)->query('SELECT id,expire FROM #{@table@} WHERE grantee = :grantee and expire > :expire', [
            'grantee'=>$user ?? $this->id,
            'expire'=>time(),
        ])->fetch()) {
            (new SessionTable)->updateByPrimaryKey($session['id'], ['expire'=>time()]);
        }
        Context::getInstance()->removeVisitor();
        hook()->exec('support:visitor::signout', [$this]);
    }
    
    /**
     * 检查是否是登陆状态
     *
     * @param integer $id
     * @param string $token
     * @return boolean
     */
    public function isLive(?int $user=null, ?string $token=null):bool
    {
        if (is_null($user)) {
            $data = [
                'grantee'=>$this->id,
                'token'=>$this->token,
                'expire'=>time()
            ];
            $user = $this->id;
        } else {
            $data = [
                'grantee'=>$user,
                'token'=>$token,
                'expire'=>time()
            ];
        }
        if ($session = (new SessionTable)->query('SELECT id FROM #{@table@} WHERE grantee = :grantee and token = :token and expire > :expire', $data)->fetch()) {
            return true;
        }
        return false;
    }

    /**
     * 刷新权限
     *
     * @return void
     */
    public function refershPermission()
    {
        $this->permission = self::loadPermission($this->id);
        return $this;
    }

    /**
     * 获取权限
     *
     * @param integer $id
     * @return Permission
     */
    protected function loadPermission(int $id):Permission
    {
        $grant=(new GrantTable)->getTableName();
        $permissions=(new RoleTable)->query('SELECT permission FROM #{@table@} JOIN  #{'.$grant.'} ON #{'.$grant.'}.grant = #{@table@}.id WHERE grantee = :grantee', ['grantee'=>$id])->fetchAll();
        if ($permissions) {
            $permission=new Permission;
            foreach ($permissions as $item) {
                if ($item['permission'] instanceof Permission) {
                    $permission->merge($item['permission']);
                }
            }
            return $permission;
        } else {
            return new Permission;
        }
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getExpire()
    {
        return $this->expireTime;
    }

    public function setPermission(Permission $permission)
    {
        $this->permission=$permission;
        return $this;
    }

    public function getPermission()
    {
        return $this->isSimulateMode()?$this->simulate->permission:$this->permission;
    }

    public function isGuest()
    {
        return $this->id == 0 || $this->expireTime < time();
    }

    public function getMaskToken()
    {
        return $this->encodeToken($this->id, $this->token);
    }

    /**
     * 检查函数权限
     *
     * @param [type] $method 输入参数
     * @return boolean
     */
    public function canAccess($method)
    {
        if ($permission=Permission::createFromFunction($method)) {
            return $this->hasPermission($permission);
        }
        return true;
    }

    /**
     * 权限对比
     *
     * @param integer $visitor
     * @return void
     */
    public function powerCompare(int $visitor)
    {
        return $this->hasPermission(self::loadPermission($visitor));
    }
    

    /**
     * 需要权限信息
     *
     * @param array|string|Permission $permission
     * @return void
     */
    public function requirePermission($permission)
    {
        $permission = Permission::buildPermission($permission);
        if (!$this->hasPermission($permission)) {
            throw new PermissionExcepiton(__('require permission $0', $permission));
        }
    }

    public function hasPermission($permission)
    {
        $check=$this->getPermission()->surpass(Permission::buildPermission($permission));
        debug()->trace(__('check_access $0', $check), ['visitor'=>$this->getPermission(),'need'=>$permission]);
        return $check;
    }

    public static function loadFromDB(int $userId)
    {
        $visitor = new Visitor($userId);
        if ($session = (new SessionTable)->select(['token','expire'], 'grantee = :grantee and expire > :expire', ['grantee'=>$userId,'expire'=> time() ])->fetch()) {
            $visitor->token = $session['token'];
            $visitor->expireTime = $session['expire'];
            return $visitor;
        }
        return null;
    }

    private static function encodeToken(int $id, string $token)
    {
        // 32-bit-Pack
        $idnum=bin2hex(pack('N', $id^Visitor::MASK));
        return base64_encode(hex2bin($idnum.$token));
    }

    private static function decodeToken(string $tokenstr)
    {
        $tokenstr=bin2hex(base64_decode($tokenstr));
        if (strlen($tokenstr)!=40) {
            return false;
        }
        $idnum=substr($tokenstr, 0, 8);
        $id=unpack('Nid', hex2bin($idnum))['id'];
        return [$id^Visitor::MASK,substr($tokenstr, 8, 32)];
    }

    public static function initVisitor()
    {
        Context::getInstance()->loadVisitor();
    }

    public static function initLocate()
    {
        $locate = cookie()->get(conf('session.language', '__lang'), conf('app.language', 'zh-CN'));
        \suda\core\Locale::set($locate);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'permission' => $this->permission,
            'simulate' => $this->simulate,
            'expireTime' => $this->expireTime,
        ];
    }
}
