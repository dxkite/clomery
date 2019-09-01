<?php

namespace support\openmethod;

use Countable;
use ArrayIterator;
use ReflectionMethod;
use IteratorAggregate;
use ReflectionFunction;
use suda\framework\Config;
use suda\application\Application;
use support\openmethod\exception\PermissionException;

/**
 * 二级权限验证
 */
class Permission implements \JsonSerializable, IteratorAggregate, Countable
{
    // 权限表,包含所有的权限结构
    protected static $permissionTable = [];
    // 权限配置
    protected static $permissionConfig = [];
    // 所有权限列表，过滤用
    protected static $permissionFilter = [];
    // 私有权限（完整权限）
    protected $permissions = [];

    public function __construct(array $permissions = null)
    {
        if (!empty($permissions)) {
            // 字符串数组
            if (is_string(current($permissions))) {
                $this->permissions = $this->filter($permissions);
            } elseif (current($permissions) instanceof Permission) {
                // 合并权限
                $this->mergeArrays($permissions);
            }
        }
        $this->permissions = $this->minimum();
    }

    /**
     * 添加权限
     *
     * @param string $name 父级权限
     * @param array $permissions 子集权限
     * @return void
     */
    public static function set(string $name, array $permissions)
    {
        static::$permissionFilter[] = $name;
        foreach ($permissions as $permission) {
            static::$permissionFilter[] = $name . '.' . $permission;
        }
        static::$permissionTable[$name] = $permissions;
    }

    public function merge(Permission $anthor_vargs)
    {
        $anthor_vargs = func_get_args();
        $this->mergeArrays($anthor_vargs);
    }

    private function mergeArrays(array $anthor_vargs)
    {
        foreach ($anthor_vargs as $anthor) {
            if ($anthor instanceof Permission) {
                $this->permissions = array_merge($this->permissions, $anthor->permissions);
            }
        }
        $this->permissions = $this->minimum();
    }

    /**
     * 检查权限操作
     *
     * @param \support\openmethod\Permission $anthor
     * @return bool
     */
    public function surpass(Permission $anthor): bool
    {
        return count($this->need($anthor)) === 0;
    }

    /**
     * 权限断言
     *
     * @param \support\openmethod\Permission $anthor
     */
    public function assert(Permission $anthor)
    {
        $need = $this->need($anthor);
        if (count($need) > 0) {
            throw new PermissionException($need);
        }
    }

    /**
     * 检查缺失的权限
     *
     * @param \support\openmethod\Permission $anthor
     * @return array
     */
    public function need(Permission $anthor)
    {
        if (empty($this->permissions) && empty($anthor->permissions)) {
            return [];
        }
        list($this_parent, $this_childs) = $this->explode($this->permissions);
        list($anthor_parent, $anthor_childs) = $this->explode($anthor->permissions);
        // a有的t没有，且t有的a全有
        $need = array_diff($anthor_parent, $this_parent);
        if (count($need) > 0) {
            return $need;
        }
        foreach ($this_parent as $parent) {
            $anthor_childs = $this->removeChilds($parent, $anthor_childs);
        }
        $need = array_diff($anthor_childs, $this_childs);
        if (count($need) > 0) {
            return $need;
        }
        return [];
    }

    /**
     * 检查是否包含单个权限name
     *
     * @param string $name
     * @return boolean
     */
    public function has(string $name)
    {
        list($this_parent, $this_childs) = $this->explode($this->permissions);
        if (static::isParent($name)) {
            return in_array($name, $this_parent);
        } elseif (in_array($name, $this_childs)) {
            return true;
        } else {
            foreach ($this_parent as $parent) {
                if (static::isChild($parent, $name)) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function isParent(string $name)
    {
        return in_array($name, array_keys(static::$permissionTable));
    }

    private static function isChild(string $parent, string $child)
    {
        if (strpos($child, '.')) {
            list($p, $c) = explode('.', $child, 2);
            if ($parent == $p) {
                return true;
            }
        }
        if (static::isParent($parent)) {
            return in_array($child, static::$permissionTable[$parent]);
        }
        return false;
    }

    private function explode(array $permission)
    {
        list($parent, $parentChilds) = $this->getParentChilds($permission);
        // 去除父级子元素
        foreach ($parent as $space) {
            unset($parentChilds[$space]);
        }
        $childs = [];
        foreach ($parentChilds as $space => $value) {
            if ($this->canLevelUp($space, $value)) {
                $parent[] = $space;
                unset($parentChilds[$space]);
            } else {
                $childs = array_merge($childs, $parentChilds[$space]);
            }
        }
        return [$parent, $childs];
    }

    protected function removeChilds(string $parent, array $childs)
    {
        return \array_filter($childs, function ($child) use ($parent) {
            if (strpos($child, $parent . '.') !== 0) {
                return true;
            }
            return false;
        });
    }

    protected function canLevelUp(string $parent, array $childs)
    {
        if (array_key_exists($parent, static::$permissionTable) && count(static::$permissionTable[$parent]) === count($childs)) {
            return true;
        }
        return false;
    }

    protected function getParentChilds(array $permission)
    {
        $parent = [];
        $childs = [];
        foreach ($permission as $index => $name) {
            if ($this->isParent($name)) {
                $parent[] = $name;
            } else {
                list($space, $x) = \explode('.', $name, 2);
                $childs[$space][] = $name;
            }
        }
        return [$parent, $childs];
    }

    private function filter(array $in)
    {
        return array_unique(array_filter($in, function ($name) {
            return in_array($name, Permission::$permissionFilter);
        }));
    }

    public function getSystemPermissions()
    {
        return array_keys(static::$permissionTable);
    }

    public function jsonSerialize()
    {
        $permissions = [];
        foreach ($this->minimum() as $value) {
            $permissions[$value] = static::alias($value);
        }
        return $permissions;
    }

    public function minimum(): array
    {
        list($this_parent, $this_childs) = $this->explode($this->permissions);
        return array_merge($this_parent, $this_childs);
    }

    public static function readPermissions(Application $app)
    {
        $permissions = [];
        foreach ($app->getModules() as $fullName => $module) {
            if ($path = $module->getResource()->getConfigResourcePath('config/permissions')) {
                $app->debug()->debug('load {module} permission from {path}', ['module' => $fullName, 'path' => $path]);
                $tmp = Config::loadConfig($path, [
                    'module' => $fullName,
                    'config' => $module->getConfig(),
                ]);
                if (is_array($tmp)) {
                    $permissions = array_merge($permissions, $tmp);
                }
            }
        }
        foreach ($permissions as $parent => $child) {
            static::set($parent, array_keys($child['childs']));
        }
        static::$permissionConfig = $permissions;
        return $permissions;
    }

    public static function alias(string $permission): string
    {
        if (strpos($permission, '.')) {
            list($parent, $child) = explode('.', $permission, 2);
            if (static::isParent($parent) && static::isChild($parent, $child)) {
                return static::$permissionConfig[$parent]['childs'][$child];
            }
        } else {
            return static::$permissionConfig[$permission]['name'];
        }
        return $permission;
    }

    /**
     * 反射读取函数执行权限
     *
     * @param ReflectionMethod|ReflectionFunction|array|string $method 可调用的函数
     * @return Permission|null
     */
    public static function createFromFunction($method): ?Permission
    {

        // -[x] authname,groupname
        // -[x] group.authname
        // -[x] group.*
        // -[x] group.[auth1,auth2]
        try {
            if ($method instanceof \ReflectionMethod || $method instanceof \ReflectionFunction) {
            } elseif (is_array($method) && count($method) > 1) {
                $method = new ReflectionMethod($method[0], $method[1]);
            } elseif (is_array($method) && count($method) === 1) {
                $method = new ReflectionFunction($method[0]);
            } else {
                $method = new ReflectionFunction($method);
            }
        } catch (\ReflectionException $e) {
            return null;
        }
        $docs = $method->getDocComment();
        if (is_string($docs) && preg_match('/@ACL(?:\s+(.+?)?\s*)?$/im', $docs, $match)) {
            $acl = null;
            if (isset($match[1])) {
                $permissions = preg_replace_callback('/([^.,]+)\.\[([^.]+)\]/', function ($matchs) {
                    list($all, $parent, $childs) = $matchs;
                    $acls = explode(',', trim($childs, ','));
                    $premStr = '';
                    foreach ($acls as $perm) {
                        $premStr .= $parent . '.' . $perm . ',';
                    }
                    return $premStr;
                }, $match[1]);
                $acl = explode(',', trim($permissions, ','));
            }
            return new Permission($acl);
        }
        return null;
    }

    /**
     * 构建权限
     * @param array|string|Permission $permission
     * @return Permission
     */
    public static function buildPermission($permission): Permission
    {
        if (!$permission instanceof Permission) {
            if (is_array($permission)) {
                $permission = new Permission($permission);
            } else {
                $permission = new Permission([$permission]);
            }
        }
        return $permission;
    }

    public function __toString()
    {
        return json_encode($this->permissions);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->permissions);
    }

    public function count()
    {
        return count($this->permissions);
    }
}
