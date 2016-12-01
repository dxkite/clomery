<?php
/**
     * 验证邮箱
     */


return api_permission('', function ( $param) {
    return api_check_callback($param,array (
  'email' => 'string',
),'model\User::checkEmail');});

