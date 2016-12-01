<?php



return api_permision('', function ( $param) {
    return api_check_callback($param,array (
),'model\User::count');});

