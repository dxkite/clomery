<?php
if ($info=Common_User::hasSignIn()) {
    var_dump($_FILES['upload']);
    Upload::setUid($info['uid']);
    var_dump($zipfile=Upload::uploadFile('upload',0));
    if ($zipfile) {
        // 添加本地的zip文章到数据库
         $md = new Blog_MdManager;
        var_dump($fileinfo=Upload::getFile($zipfile));
        var_dump($aid=$md->uploadZipMarkdown($fileinfo['path'], $fileinfo['name']));
        var_dump($md->uploadInfo());
        var_dump(PageUrl::article((int)$aid));
        var_dump($md->uploads);
        if ($aid) {
            echo '上传成功';
        } elseif ($aid ==0) {
            echo '相同文章已经存在';
        }
    } else {
        echo '上传失败';
    }
} else {
    Page::redirect('/user/SignIn');
}
