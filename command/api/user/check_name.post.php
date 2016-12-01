<?php



return api_permision('', function ( $param) {
    return api_check_callback($param,array (
  'name' => 'string',
),'model\User::checkName');});

