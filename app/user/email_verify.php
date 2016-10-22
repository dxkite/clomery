<?php
function verify($uid, $verify)
{
    Common_User::verify((int)$uid, $verify);
    if (Common_User::emailVerified($uid)) {
        echo '验证成功';
    } else {
        echo '验证失败';
    }
}
