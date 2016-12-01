<?php

return api_permission(true, function ( $param) {
    if ($param['user_id'] != intval($param['id']) ){
      return new api\Error('permissionDenied','permission denied');
    }
    return api_check_callback($param,array (
  'id' => 'int',
  'resource_id' => 'int',
),'model\User::setAvatar');});

