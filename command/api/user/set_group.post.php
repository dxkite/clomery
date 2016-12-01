<?php
/**
    * @Auth:admin
    */


return api_permission('admin', function ( $param) {
    return api_check_callback($param,array (
  'id' => 'int',
  'group' => 'int',
),'model\User::setGroup');});

