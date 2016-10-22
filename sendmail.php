<?php
defined('DOC_ROOT') or define('DOC_ROOT', __DIR__);
require_once 'core/XCore.php';
    // APP 相关数据
    defined('WEB_ROOT') or define('WEB_ROOT', DOC_ROOT.'/public');
    // 语言文件夹
    defined('APP_LANG')or define('APP_LANG', APP_RES.'/lang');
    // 视图控制
    defined('APP_VIEW')or define('APP_VIEW', APP_RES.'/view');
    defined('APP_TPL')or define('APP_TPL', APP_RES.'/tpl');
    // 临时文件
    defined('APP_TMP')or define('APP_TMP',  APP_RES.'/tmp');

    defined('INSTALL_LOCK') or define('INSTALL_LOCK', 'install.lock');
    defined('APP_VISIT') or define('APP_VISIT', '.visit.php');

View::loadCompile();
// 获取网站设置
Site_Options::init();
$op=new Site_Options;
// 语言支持
Page::language(Cookie::get('lang', 'zh_cn'));
View::theme(Site_Options::getTheme());
// 载入页面URL配置规则
require_once APP_ROOT.'/'.APP_VISIT;
function sendtouser($uid)
{
    if ($info=Common_User::getBaseInfo($uid)) {
        $return=($mail=new Mail())
                ->from('usercenter@atd3.cn', '用户中心')
                ->to($info['email'], $info['uname'])
                ->subject('DxCore 邮箱验证')
                ->use('mail')
                ->send([
                    'title'=>'来至 DxCore 的验证邮箱',
                    'site_name'=>'DxCore',
                    'message'=>'欢迎注册DxCore账号！',
                    'user'=>$info['uname'],
                    'verify'=>PageUrl::verifyMailUrl($uid, Common_User::createVerify($uid)),
                    'hosturl'=>'//atd3.cn',
                    'hostname'=>'atd3.cn',
                ]);
        var_dump($return);
        var_dump($mail->errno(), $mail->error());
    }
}
$argv=$_SERVER['argv'];
var_dump($argv);
sendtouser($argv[1]);
