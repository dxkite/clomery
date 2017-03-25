<?php
namespace cn\atd3;



class Api
{
    /* 接口权限检查 */

    public static function permission($permissions)
    {
        
        $id=User::getUserId();
        if ($id) {
            if (!User::hasPermission($id, $permissions)) {
                throw new ApiException('hasNoPermission', 'no permission');
            }
        } else {
            throw new ApiException('hasNoUserToken', 'need user info to check permission');
        }
        return true;
    }
    /*API 接口参数检查 */
    public static function check($value_input, array $checks)
    {
        if (!$value_input) {
            throw new ApiException('nullInput', 'please check the json!');
        }
        $param=[];
        foreach ($checks as $key=>$value) {
            // ['name']
            // ['name'=>'type']
            // ['name'=>['type','default']]

            if (is_numeric($key)) {
                $name=$value;
                $type='string';
                $has_default=false;
            } else {
                $name=$key;
                if (is_array($value)) {
                    $type= array_shift($value);
                    if (count($value)) {
                        $default=array_shift($value);
                        $has_default=true;
                    } else {
                        $has_default=false;
                    }
                } else {
                    $type=$value;
                    $has_default=false;
                }
            }
            
            if (is_array($value_input)) {
                if (!isset($value_input[$name]) &&  !$has_default) {
                    throw new ApiException('paramPassException', 'need '.$name);
                } else {
                    $val=isset($value_input[$name])?$value_input[$name]:$default;
                    if (@settype($val, $type)) {
                        $param[$name]=$val;
                    } else {
                        throw new ApiException('paramTypeCastException', $name .' cannot be '.$type);
                    }
                }
            } elseif (is_object($value_input)) {
                if (!isset($value_input->$name) &&   !$has_default) {
                    throw new ApiException('paramPassException', 'need '.$name);
                } else {
                    $val=isset($value_input->$name)?$value_input->$name:$default;
                    if (@settype($val, $type)) {
                        $param[$name]=$val;
                    } else {
                        throw new ApiException('paramTypeCastException', $name .' cannot be '.$type);
                    }
                }
            }
        }

        return $param;
    }
}
