<?php

class Language extends \Core\Value
{
    function __construct(string $lang)
    {
        $langs=parse_ini_file(APP_LANG.'/'.$lang.'.lang');
        parent::__construct($langs);
    }
}