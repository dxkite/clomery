<?php
namespace support\visitor;

use support\openmethod\Permission;

class Visitor
{
    /**
     * 属性
     *
     * @var array|\ArrayAccess
     */
    protected $attribute;

    /**
     * 用户ID
     *
     * @var string
     */
    protected $id;

    /**
     * 权限
     *
     * @var Permission
     */
    protected $permission;

    /**
     * 创建访问者
     *
     * @param string $id
     * @param array|\ArrayAccess $attribute
     */
    public function __construct(string $id = '',  $attribute = [])
    {
        $this->id = $id;
        $this->attribute = $attribute;
    }

    /**
     * 获取属性
     *
     * @return  mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attribute[$name] ?? $default;
    }

    /**
     * 设置属性
     *
     * @param string $name
     * @param mixed $attribute
     * @return self
     */
    public function setAttribute(string $name, $attribute)
    {
        $this->attribute[$name] = $attribute;
        return $this;
    }
    
    /**
     * 设置全部属性
     *
     * @param  array  $attribute  属性
     * @return  self
     */
    public function setAttributes(array $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }
    
    /**
     * 获取属性
     *
     * @return  mixed
     */
    public function getAttributes()
    {
        return $this->attribute;
    }
    
    /**
     * Get 用户ID
     *
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get 权限
     *
     * @return  Permission
     */
    public function getPermission():Permission
    {
        return $this->permission;
    }

    /**
     * Set 用户ID
     *
     * @param  string  $id  用户ID
     *
     * @return  self
     */
    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param $method
     * @return bool
     */
    public function canAccess($method) : bool
    {
        if ($permission = Permission::createFromFunction($method)) {
            return $this->hasPermission($permission);
        }
        return true;
    }

    /**
     * @param array|Permission|string $permission
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        if (!$permission instanceof Permission) {
            if (is_array($permission)) {
                $permission = new Permission($permission);
            } elseif (is_string($permission)) {
                $permission = new Permission([$permission]);
            } else {
                return false;
            }
        }
        $check = $this->getPermission()->surpass($permission);
        return $check;
    }

    public function isGuest():bool
    {
        return strlen($this->id) === 0;
    }

    /**
     * Set 权限
     *
     * @param  Permission  $permission  权限
     *
     * @return  self
     */ 
    public function setPermission(Permission $permission)
    {
        $this->permission = $permission;

        return $this;
    }
}
