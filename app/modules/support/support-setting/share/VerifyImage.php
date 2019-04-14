<?php
namespace support\setting;

use support\setting\Context;
use suda\framework\filesystem\FileSystem;
use support\setting\VerifyImageGenerator;

class VerifyImage
{
    private static $verifyschars = 'mnbvcxzasdfghjklpoiuytrewq1234567890QWERTYUIOPLKJHGFDSAZXCVBNM';

    /**
     * 当前字体文件路径
     *
     * @var string
     */
    private $foint;
    
    /**
     * 内容环境
     *
     * @var Context
     */
    private $context;

    /**
     * 验证码名
     *
     * @var string
     */
    private $name;

    public function __construct(Context $context, string $name)
    {
        $path = $context->getApplication()->getModules()->getModuleFromPath(__FILE__)->getResource()->getResourcePath('ttf');
        $fointit = FileSystem::readFiles($path);
        $foints = iterator_to_array($fointit);
        $this->foint = $foints[mt_rand(0, count($foints) - 1)];
        $this->context = $context;
        $this->name = $name;
    }

    public function display()
    {
        $vecode = new VerifyImageGenerator($this->foint, 18, 4);
        $randCode = $vecode->generate();
        $vecode->display(VerifyImageGenerator::IMG_JPG);
        $this->context->getSession()->set($this->name, strtoupper($randCode));
    }

    public function hasCode()
    {
        return $this->context->getSession()->has($this->name);
    }

    public function checkCode(string $code):bool
    {
        $verify = $this->context->getSession()->get($this->name);
        if (strlen($verify) === 0 || strlen($code) === 0) {
            return false;
        }
        $result = strtoupper($verify) === strtoupper($code);
        $this->refresh();
        return $result;
    }

    public function refresh()
    {
        $this->context->getSession()->set($this->name, null);
    }
}
