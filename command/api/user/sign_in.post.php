<?php



return api_permision('', function ( $param) {
    return api_check_callback($param,array (
  'name' => 'string',
  'password' => 'string',
  'client' => 'int',
  'client_token' => 'string',
),'model\User::signIn');});

