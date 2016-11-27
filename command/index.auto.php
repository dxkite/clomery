<?php

$insert=Query::insert('user_token',['uid'=>1,'name'=>'DXkite']);
var_dump(Query::where('user_token','tid')->fetchAll());

