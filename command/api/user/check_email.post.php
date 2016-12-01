<?php
// 验证邮箱是否存在
return api_permission('', function ( $param) {
    return api_check_callback($param,array (
  'email' => 'string',
),'model\User::checkEmail');});

