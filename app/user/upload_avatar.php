<?php
if ($info=UManager::hasSignIn()) {
    var_dump($_FILES['upload']);
    var_dump($avatar=Upload::uploadFile('upload', $info['uid'], 1));
    if ($avatar) {
        var_dump(UManager::setAvatar($info['uid'], $avatar));
        echo '上传成功';
    } else {
        echo '上传失败';
    }
    Page::redirect('/user');
} else {
    Page::redirect('/user/SignIn');
}
