<?php



return api_permission('', function ( $param) {
    return api_check_callback($param,array (
  'id' => 'int',
  'name' => 'string',
),'model\User::hasPermission');});

