<?php
class Image
{
    public static $verifyschars='mnbvcxzasdfghjklpoiuytrewq1234567890QWERTYUIOPLKJHGFDSAZXCVBNM';
    public $foint;

    public function __construct()
    {
        $foints=Storage::readDirFiles(__DIR__.'/ttf/');
        $this->foint=$foints[mt_rand(0, count($foints)-1)];
    }
    /*
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
*/

    public function verifyImage()
    {
        $size=4;
        $randCode = '';
        for ($i = 0; $i < $size; $i++) {
            $randCode .= substr(self::$verifyschars, mt_rand(0, strlen(self::$verifyschars) - 1), 1);
        }
        Session::set('human_varify', strtoupper($randCode));
        $img = imagecreate(80, 25);
        $bgColor =  imagecolorallocate($img, mt_rand(245, 255), mt_rand(245, 255), mt_rand(245, 255)) ;
        
        for ($i = 0; $i < $size; $i++) {
            $x = $i * 14 + mt_rand(4, 8) +10;
            $y = mt_rand(18, 22);
            $text_color = imagecolorallocate($img, mt_rand(30, 180), mt_rand(10, 100), mt_rand(40, 250));
            imagettftext($img, mt_rand(12, 14), mt_rand(0, 30), $x, $y, $text_color, $this->foint, $randCode[$i]);
        }

        for ($j = 0; $j < 60; $j++) {
            $pixColor = imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 200), mt_rand(40, 250));
            $x = mt_rand(0, 80);
            $y = mt_rand(0, 25);
            imagesetpixel($img, $x, $y, $pixColor);
        }
        imagepng($img);
        imagedestroy($img);
    }
    function checkCode(string $code):bool
    {
        return Session::get('human_varify')===strtoupper($randCode);
    }
}
