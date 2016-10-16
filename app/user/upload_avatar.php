<?php
if ($info=DB_User::hasSignIn()) {
    var_dump($_FILES['upload']);
    Upload::setUid($info['uid']);
    var_dump($avatar=Upload::uploadFile('upload', 1));
    if ($avatar) {
        var_dump(DB_User::setAvatar($info['uid'], $avatar));
        echo '上传成功';
    } else {
        echo '上传失败';
    }
    Page::redirect('/user');
} else {
    Page::redirect('/user/SignIn');
}
