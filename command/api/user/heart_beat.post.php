<?php



return api_permision('', function ( $param) {
    return api_check_callback($param,array (
  'token_id' => 'int',
  'token' => 'string',
),'model\User::heartBeat');});

