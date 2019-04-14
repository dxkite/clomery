<?php
namespace support\setting;

/**
 * 验证码生成器
 * 生成流程：
 * 1. 随机生成字符串
 * 2. 字符串偏移错位
 * 3. 计算画布大小
 * 4. 绘制文字
 * 5. 绘制干扰
 */
class VerifyImageGenerator
{
    const USE_ZH = 0;
    const USE_EN = 1;
    
    const IMG_PNG = 0;
    const IMG_JPG = 1;
    const IMAGE_HEIGHT = 40;
    const IMAGE_WIDTH = 100;
    protected $length = 4;
    protected $codes = [];
    protected $codeStr = '';
    protected $imageSize = [];
    private $fontSize = 18;
    private $fontFile = null;
    
    public function __construct(string $font, int $fontsize = 18, int $leng = 4)
    {
        $this->length = $leng;
        $this->fontSize = $fontsize;
        $this->fontFile = $font;
    }

    /**
     * 生成验证码
     *
     * @param string $code
     * @return string
     */
    public function generate(string $code = null):string
    {
        $codes = [];
        // 生成文字
        if (null === $code) {
            for ($i = 0; $i < $this->length; $i++) {
                // 英文~数字
                $codes [] = rand() % 2?chr(mt_rand(49, 57)):chr(mt_rand(65, 90));
            }
        } else {
            $codes[] = $code;
        }
        $fz = $this->fontSize;
        $padding = $fz * 0.5;
        $imageWidth = $padding;
        $imageHeight = 0;
        foreach ($codes as $index => $code) {
            $angle = mt_rand(0, 30);
            $bbox = imagettfbbox($this->fontSize, $angle, $this->fontFile, $code);
            $xlen = [$bbox[0],$bbox[2],$bbox[4],$bbox[6]];
            $ylen = [$bbox[1],$bbox[3],$bbox[5],$bbox[7]];
            sort($xlen);
            sort($ylen);
            $w = $xlen[3] - $xlen[0];
            $h = $ylen[3] - $ylen[0];
            $this->codes[$index]['text'] = $code;
            $this->codes[$index]['angle'] = $angle;
            $this->codes[$index]['width'] = $w;
            $this->codes[$index]['heigth'] = $h;
            $this->codes[$index]['x'] = $imageWidth;
            $this->codes[$index]['y'] = $h + $padding;
            $imageWidth += $w;
            if ($h > $imageHeight) {
                $imageHeight = $h;
            }
        }
        $this->imageSize = [$imageWidth + $padding ,$imageHeight + $padding * 2];
        return $this->codeStr = implode('', $codes);
    }
 
    /**
     * 获取验证码
     *
     * @return string
     */
    public function getCode():string
    {
        return $this->codeStr;
    }

    /**
     * 渲染图片
     *
     * @param integer $type
     * @param string|null $file
     * @return void
     */
    public function display(int $type, ?string $file = null)
    {
        $img = imagecreate($this->imageSize[0], $this->imageSize[1]);
        $bgColor = imagecolorallocate($img, mt_rand(245, 255), mt_rand(245, 255), mt_rand(245, 255));
        foreach ($this->codes as $info) {
            $color = imagecolorallocate($img, mt_rand(30, 180), mt_rand(10, 100), mt_rand(40, 250));
            imagettftext($img, $this->fontSize, $info['angle'], $info['x'], $info['y'], $color, $this->fontFile, $info['text']);
            imagearc($img, mt_rand(0, 80), mt_rand(30, 80), mt_rand(30, 180), mt_rand(40, 180), mt_rand($this->imageSize[0], 180), mt_rand($this->imageSize[1], 180), $color);
        }
        for ($j = 0; $j < 100; $j++) {
            $pixColor = imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 200), mt_rand(40, 250));
            $x = mt_rand(0, $this->imageSize[0]);
            $y = mt_rand(0, $this->imageSize[1]);
            imagesetpixel($img, $x, $y, $pixColor);
        }
        $thumb = imagecreate(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        imagecopyresized($thumb, $img, 0, 0, 0, 0, self::IMAGE_WIDTH, self::IMAGE_HEIGHT, $this->imageSize[0], $this->imageSize[1]);
        if ($type == IMG_PNG) {
            imagepng($thumb);
        } else {
            imagejpeg($thumb);
        }
        imagedestroy($thumb);
        imagedestroy($img);
    }
}
