<?php
// 获取用户数量
return api_permission('', function ( $param) {
    return api_check_callback($param,array (
),'model\User::count');});

