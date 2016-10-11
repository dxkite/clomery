<?php
if (isset($_GET['img'])) {
     header("Content-type: image/png");
     $im=imagecreatefrompng($_GET['img']);
     $text_color = imagecolorallocate($im, 255, 0, 0);
     imagestring($im, 16, imagesx($im)-16*13, imagesy($im)-16,  "from atd3.cn", $text_color);
     imagepng($im);
     imagedestroy($im);
 } else {
    echo 'No Image';
}
