<?php
namespace cn\atd3\api;

class Hook
{
    public static function setting($template)
    {
        $template->addcommand('apibase', function ($exp) {
            if(preg_match('/\((.+)\)/', $exp, $v)){
                $name=trim($v[1], '"\'');
                return u('api:'.$name);
            }
            else{
                return  request()->hostBase().router()->getModulePrefix('api')[1];
            }
        });
    }
}
