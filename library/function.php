<?php
/*API 接口参数检查 */

function api_check_values($value_input, array $checks, $callback)
{
    $param=[];
    foreach ($checks as $key=>$value) {
        // ['name']
        // ['type'=>'name']
        // ['type'=>['name','default']]
        if (is_numeric($key)) {
            $name=$value;
            $type='string';
            $has_default=false;
        } else {
            $type=$key;
            if (is_array($value)) {
                $name=$value[0];
                $default=$value[1];
                $has_default=true;
            } else {
                $name=$value[0];
                $has_default=false;
            }
        }

        if (is_array($value_input)) {
            if (!isset($value_input[$name]) &&  !$has_default) {
                return new api\Error('paramError', 'need '.$name);
            } else {
                $val=isset($value_input[$name])?$value_input[$name]:$default;
                if (settype($val, $type)) {
                    $param[$name]=$val;
                } else {
                    return new api\Error('paramTypeCastError', $name .' cannot be '.$type);
                }
            }
        } elseif (is_object($value_input)) {
            if (!isset($value_input->$name) &&   !$has_default ) {
                return new api\Error('paramError', 'need '.$name);
            } else {
                $val=isset($value_input->$name)?$value_input->$name:$default;

                if (settype($val, $type)) {
                    $param[$name]=$val;
                } else {
                    return new api\Error('paramTypeCastError', $name .' cannot be '.$type);
                }
            }
        }
    }
    $return = (new server\Command($callback))->exec($param);
    if ($return instanceof api\Error) {
        return $return;
    } elseif ($return === false) {
        return new api\Error('returnFalse','method return false,please check the percondition of use this api!');
    } else {
        return new api\Success($return);
    }
}
