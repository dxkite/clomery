<?php
if (isset($_GET['id']) && $_GET['id'] > 0){
    Upload::outputPublic($_GET['id']);
}
else{
    echo 'No ID';
}