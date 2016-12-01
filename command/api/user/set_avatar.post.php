<?php



return api_permission('', function ( $param) {
    return api_check_callback($param,array (
  'id' => 'int',
  'resource_id' => 'int',
),'model\User::setAvatar');});

