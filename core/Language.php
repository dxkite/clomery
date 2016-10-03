<?php

class Language extends \Core\Value
{
    // TODO : 把空值作为缺省
    public function __construct(string $lang)
    {
        if (Storage::exist($path=APP_LANG.'/'.$lang.'.lang')) {
            $langs=parse_ini_file($path);
        } else {
            $langs=parse_ini_file(APP_LANG.'/zh_cn.lang');
        }
        parent::__construct($langs);
    }
    public function __call(string $name, $args)
    {
        $fmt=isset($this->var[$name])?$this->var[$name]:(isset($args[0])?$args[0]:'U:['.$name.']');
        if (count($args)>1) {
            $args[0]=$fmt;
            return call_user_func_array('sprintf', $args);
        }
        return $fmt;
    }
}
