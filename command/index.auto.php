<?php

$insert=Query::insert('user_token',['uid'=>1,'name'=>'DXkite']);
var_dump(Query::where('user_token')->fetchAll());
var_dump(Query::update('user_token',['name'=>'TTHHR']));
var_dump(Query::delete('user_token','name=:name LIMIT 1',['name'=>'TTHHR']));
