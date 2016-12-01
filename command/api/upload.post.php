<?php

return api_permission('upload', function ( $param) {
    return api_check_callback($param, ['user_id', 'state'], 'api\Upload::upload');
});
