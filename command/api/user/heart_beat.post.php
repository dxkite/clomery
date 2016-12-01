<?php

return api_permission('', function ( $param) {
    return api_check_callback($param,array (
  'token_id' => 'int',
  'token' => 'string',
),'model\User::heartBeat');});

