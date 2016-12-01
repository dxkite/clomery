<?php
/**
     * 验证邮箱
     */


return api_permision('', function ( $param) {
    return api_check_callback($param,array (
  'email' => 'string',
),'model\User::checkEmail');});

