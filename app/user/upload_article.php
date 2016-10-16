<?php
if ($info=UserManager::hasSignIn()) {
    var_dump($_FILES['upload']);
    var_dump($zipfile=Upload::uploadFile('upload', 0));
    Upload::setUid($info['uid']);
    if ($zipfile) {
        // 添加本地的zip文章到数据库
         $md = new Markdown_Manager;
        var_dump($fileinfo=Upload::getFile($zipfile));
        var_dump($aid=$md->uploadZipMarkdown($fileinfo['path'], $fileinfo['name']));
        var_dump($md->uploadInfo());
        var_dump(PageUrl::article((int)$aid));
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
