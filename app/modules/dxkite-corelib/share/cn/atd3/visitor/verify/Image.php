<?php
namespace cn\atd3\visitor\verify;

use suda\core\{Storage,Application};
use cn\atd3\user\Session;
use cn\atd3\image\VeCode;
use cn\atd3\visitor\Context;

class Image
{
    private static $verifyschars='mnbvcxzasdfghjklpoiuytrewq1234567890QWERTYUIOPLKJHGFDSAZXCVBNM';
    private $foint;
    private $context;
    private $name;

    public function __construct(Context $context,string $name)
    {
        $foints=Storage::readDirFiles(Application::getModulePath('corelib').'/resource/ttf/');
        $this->foint=$foints[mt_rand(0, count($foints)-1)];
        $this->context=$context;
        $this->name=$name;
    }

    public function display()
    {
        $vecode= new VeCode($this->foint, 18, 4);
        $randCode=$vecode->generate();
        $vecode->display(VeCode::IMG_PNG);
        $this->context->setSession($this->name, strtoupper($randCode));
    }

    public function hasCode()
    {
        return $this->context->hasSession($this->name);
    }

    public function checkCode(string $code):bool
    {
        $result=strtoupper($verify=$this->context->getSession($this->name))===strtoupper($code);
        self::refresh();
        return $result;
    }

    public function refresh()
    {
        $this->context->setSession($this->name, null);
    }
}
