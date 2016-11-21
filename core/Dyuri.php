<?php

class Dyuri
{
    public $method;
    public $url;
    public $caller;
    public $template;
    public $type;
    public $querys;
    public $options;

    public function __construct($set)
    {
        $this->method=$set[1];
        $this->url=$set[2];
        $this->caller=$set[3];
        preg_match('/^(?:\[(.*?)\])?(?:\((\w+)(?::(\w+))\))?$/',$set[4],$match);
        $this->querys=isset($match[1])?explode(',',$match[1]):[];
        $this->type=isset($match[2])?$match[2]:'json';
        $this->options=isset($set[5])?$set[5]:'';
    }
}
