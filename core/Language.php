<?php

class Language extends \Core\Value
{
    public function __construct(string $lang)
    {
        if (Storage::exsit($path=APP_LANG.'/'.$lang.'.lang')) {
            $langs=parse_ini_file($path);
        } else {
            $langs=parse_ini_file(APP_LANG.'/zh_cn.lang');
        }
        parent::__construct($langs);
    }
}
