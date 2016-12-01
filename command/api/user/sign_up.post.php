<?php



return api_permission('', function ( $param) {
    return api_check_callback($param,array (
  'name' => 'string',
  'email' => 'string',
  'password' => 'string',
  'client_id' => 'int',
  'client_token' => 'string',
  'value' => 
  array (
    0 => 'string',
    1 => '',
  ),
),'model\User::signUp');});

