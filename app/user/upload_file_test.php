<?php  if (!isset($_FILES['upload'])): ?>
<form enctype="multipart/form-data" action="/user/upload_file_test" method="POST">
    Send this file: <input name="upload" type="file" />
    <input type="submit" value="Send File" />
    <input type="radio" id="public_1" name="public" value="1" checked="checked" />
    <label for="public_1">公开</label>
    <input type="radio" id="public_0" name="public" value="0" />
    <label for="public_0">私有</label>
</form>
<?php else:
if ($info=DB_User::hasSignIn()){
    var_dump($_FILES['upload']);
    var_dump(Upload::uploadFile('upload',$info['uid'],isset($_POST['public'])?$_POST['public']:1));
}
else{
    Page::redirect('/user/SignIn');
}
endif ?>