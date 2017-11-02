<?php
namespace cn\atd3\visitor;

use suda\core\Application;
use suda\core\Storage;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionClass;
use suda\tool\Json;

class Permission implements \JsonSerializable
{
    // 权限表
    private static $permission_table=[];
    private static $permission_list=[];
    private static $readtable=false;

    private $permissions=[];

    public function __construct(array $permissions=null)
    {
        if (!self::$readtable) {
            self::readPermissions();
            self::$readtable=true;
        }
        if (!empty($permissions)) {
            // 字符串数组
            if (is_string(current($permissions))) {
                $this->permissions=$this->filter($permissions);
            } elseif (current($permissions) instanceof Permission) {
                // 合并权限
                $this->mergeArrays($permissions);
            }
        }
    }

    public static function set(string $name, array $permissions)
    {
        self::$permission_list[]=$name;
        self::$permission_list=array_merge(self::$permission_list, $permissions);
        self::$permission_table[$name]=$permissions;
    }
    
    public function merge(Permission $anthor_vargs)
    {
        $anthor_vargs=func_get_args();
        $this->mergeArrays($anthor_vargs);
    }

    private function mergeArrays(array $anthor_vargs)
    {
        foreach ($anthor_vargs as $anthor) {
            if ($anthor instanceof Permission) {
                $this->permissions=array_merge($this->permissions, $anthor->permissions);
            }
        }
    }

    public function surpass(Permission $anthor)
    {
        if (!conf('visitor-permission-check', true)) {
            return true;
        }

        if (empty($this->permissions) && empty($anthor->permissions)) {
            return true;
        }
        
        $permission=$anthor->permissions;
        list($this_parent, $this_childs)=self::splitIt($this->permissions);
        // 去除父级元素
        $permission=array_diff($permission, $this_parent);
        // 去除父级权限的子权限
        foreach ($this_parent as $parent) {
            if (isset(self::$permission_table[$parent])) {
                $permission=array_diff($permission, self::$permission_table[$parent]);
            }
            if (empty($permission)) {
                return true;
            }
        }
        if (count(array_diff($permission, $this_childs))) {
            return false;
        }
        return true;
    }

    public function has(string $name)
    {
        list($this_parent, $this_childs)=self::splitIt($this->permissions);
        if ($this->isParent($name)) {
            return is_array($name, $this_parent);
        } elseif (in_array($name, $this_childs)) {
            return true;
        } else {
            foreach ($this_parent as $parent) {
                if ($this->isChild($parent, $name)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function isParent(string $name)
    {
        return in_array($name, array_keys(self::$permission_table));
    }

    private function isChild(string $parent, string $child)
    {
        if (self::isParent($parent)) {
            return in_array($child, self::$permission_table[$parent]);
        }
        return false;
    }

    private function splitIt(array $permission)
    {
        $parent=[];
        // 去除父级元素
        foreach ($permission as $index=> $perm) {
            if ($this->isParent($perm)) {
                $parent[]=$perm;
                unset($permission[$index]);
            }
        }

        // 去除父级权限的子权限
        foreach ($parent as $index) {
            if (isset(self::$permission_table[$index])) {
                $permission=array_diff($permission, self::$permission_table[$index]);
            }
            if (empty($permission)) {
                break;
            }
        }
        // 父级，子集
        return [$parent,$permission];
    }

    private function filter(array $in)
    {
        return array_diff($in, array_diff($in, self::$permission_list));
    }

    public function jsonSerialize()
    {
        list($this_parent, $this_childs)=self::splitIt($this->permissions);
        return array_merge($this_parent, $this_childs);
    }

    public static function readPermissions()
    {
        $modules=Application::getLiveModules();
        $permissions=[];
        foreach ($modules as $module) {
            if (Storage::exist($jsonfile=Application::getModulePath($module).'/resource/config/permissions.json')) {
                $tmp=Json::loadFile($jsonfile);
                $permissions=array_merge($permissions, $tmp);
            }
        }
        foreach ($permissions as $surp=>$child) {
            self::set($surp, array_keys($child['childs']));
        }
        return $permissions;
    }

    public static function createFromFunction($method)
    {
        if ($method instanceof \ReflectionMethod || $method instanceof \ReflectionFunction) {
        } elseif (count($method)>1) {
            $method=new ReflectionMethod($method[0], $method[1]);
        } else {
            $method=new ReflectionFunction($method);
        }
        $docs=$method->getDocComment();
        if ($docs && preg_match('/@ACL\s+([\w,]+)?\s*$/im', $docs, $match)) {
            $acl=null;
            if (isset($match[1])) {
                $acl=explode(',', trim($match[1], ','));
            }
            // debug()->debug('create_permission '.$method->getName(),$acl);
            return new Permission($acl);
        }
        return false;
    }
}
