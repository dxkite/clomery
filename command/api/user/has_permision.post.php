<?php



return api_permision('', function ( $param) {
    return api_check_callback($param,array (
  'id' => 'int',
  'name' => 'string',
),'model\User::hasPermission');});

