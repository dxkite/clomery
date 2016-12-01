<?php



return api_permission('', function ( $param) {
    return api_check_callback($param,array (
  'name' => 'string',
),'model\User::checkName');});

