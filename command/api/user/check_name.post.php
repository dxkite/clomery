<?php
// 验证用户名是否存在
return api_permission('', function ( $param) {
    return api_check_callback($param,array (
  'name' => 'string',
),'model\User::checkName');});

