<?php
/* 接口权限检查 */
function api_permission(string $permissions, $callback)
{
    Page::json();
    if (Request::isJson()) {
        $param=Request::json();
    } else {
        $param=$_POST;
    }

    // 开启权限检查
    if ($permissions) {
        // 配置权限信息
        if ($uid=User::getSignInUserId()) {
        } elseif (isset($param['user_id']) && isset($param['user_token'])) {
            if (model\Token::verify($param['user_id'], $param['user_token'])) {
                $uid=$param['user_id'];
            } else {
                return new api\Error('hasNoUserInfo', 'need user_id use_token to check permission');
            }
        } else {
            return new api\Error('hasNoUserInfo', 'need user_id use_token to check permission');
        }
        // 检查权限
        if (is_string($permissions)) {
            $permissions=explode(',', trim(',', $permissions));
            // 单个权限
            foreach ($permissions as $permission) {
                if (!User::hasPermission($uid, $permission)) {
                    return new api\Error('permissionDenied', $permission .':permission denied');
                }
            }
        }
    }
    return (new \server\Command($callback))->exec([$param]);
}

/*API 接口参数检查 */
function api_check_values($value_input, array $checks)
{
    if (!$value_input) {
        return new api\Error('nullInput', 'please check the json!');
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
                $type=$value[0];
                $default=$value[1];
                $has_default=true;
            } else {
                $type=$value;
                $has_default=false;
            }
        }

        if (is_array($value_input)) {
            if (!isset($value_input[$name]) &&  !$has_default) {
                return new api\Error('paramError', 'need '.$name);
            } else {
                $val=isset($value_input[$name])?$value_input[$name]:$default;
                if (@settype($val, $type)) {
                    $param[$name]=$val;
                } else {
                    return new api\Error('paramTypeCastError', $name .' cannot be '.$type);
                }
            }
        } elseif (is_object($value_input)) {
            if (!isset($value_input->$name) &&   !$has_default) {
                return new api\Error('paramError', 'need '.$name);
            } else {
                $val=isset($value_input->$name)?$value_input->$name:$default;
                if (@settype($val, $type)) {
                    $param[$name]=$val;
                } else {
                    return new api\Error('paramTypeCastError', $name .' cannot be '.$type);
                }
            }
        }
    }
    return $param;
}


function api_check_callback($value_input, array $checks, $callback)
{
    $param=api_check_values($value_input, $checks);
    if ($param instanceof api\Error) {
        return $param;
    }
    $return = (new server\Command($callback))->exec($param);
    if ( $return instanceof api\Error) {
        return $return;
    }else{
        return ['return'=> $return];
    }
}
