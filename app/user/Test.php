<?php  if (!isset($_FILES['userfile'])): ?>
<form enctype="multipart/form-data" action="/user/Test" method="POST">
    Send this file: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>
<?else:
var_dump(Upload::uploadFile('userfile',43,1));
endif ?>