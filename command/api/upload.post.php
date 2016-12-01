<?php

return api_permision('upload', function ( $param) {
    return api_check_callback($param, ['user_id', 'state'], 'api\Upload::upload');
});
