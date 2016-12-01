<?php



return api_permision('', function ( $param) {
    return api_check_callback($param,array (
  'id' => 'int',
  'group' => 'int',
),'model\User::setGroup');});

