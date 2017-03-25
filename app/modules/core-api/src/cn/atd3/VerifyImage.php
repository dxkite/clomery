<?php
namespace cn\atd3;

use suda\core\Storage;
use cn\atd3\Session;

use atd3\image\VeCode;

class VerifyImage
{
    public static $verifyschars='mnbvcxzasdfghjklpoiuytrewq1234567890QWERTYUIOPLKJHGFDSAZXCVBNM';
    public $foint;

    public function __construct()
    {
        $foints=Storage::readDirFiles(MODULE_RESOURCE.'/ttf/');
        $this->foint=$foints[mt_rand(0, count($foints)-1)];
    }

    public function create()
    {
        $vecode= new VeCode($this->foint,18,4);
        $randCode=$vecode->generate();
        $vecode->display(VeCode::IMG_PNG);
        _D()->d('Create_Code:'.strtoupper($randCode));
        Session::set('human_varify', strtoupper($randCode));
    }


    public static function checkCode(string $code):bool
    {
       $result=strtoupper(Session::get('human_varify'))===strtoupper($code);
       self::refresh();
       return $result;
    }

    public static function refresh()
    {
        Session::set('human_varify',null);
    }

    public function version()
    {
        return gd_info()['GD Version'];
    }
}
