<?php

    $v=new View\Value(['value'=>null,'key'=>'null']);
    var_dump($v->value('Hello %s','DXkite'));
    return  'Hello';
