<?php



return api_permision('', function ( $param) {
    return api_check_callback($param,array (
  'id' => 'int',
  'resource_id' => 'int',
),'model\User::setAvatar');});

