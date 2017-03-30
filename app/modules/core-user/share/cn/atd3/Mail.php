<?php
namespace cn\atd3;

class Mail
{
    private static $mail;
    private function __construct(){}
    public static function getInstance()
    {
        if(is_null(self::$mail)){
            self::$mail=self::getMailer();
        }
        return self::$mail;
    }

    private  static function getMailer(){
        // 未检测到SMTP设置
        if (conf('app.smtp',false) || !conf('smtp',false)){
            return self::$mail=new \suda\mail\Sendmail();
        }else{
            return self::$mail=new \suda\mail\Smtp(conf('smtp.server'),conf('smtp.port'),conf('smtp.timeout'),conf('smtp.auth'),conf('smtp.user'),conf('smtp.passwd'));
        }
    }

    public static function sendCheckMail($email,$code){
        return self::getInstance()
        ->to($email)
        ->subject('ATD工作室：注册邮箱验证')
        ->from(conf('smtp.email','usercenter@atd3.cn'),'ATD邮箱中心')
        ->use('api:mail')
        ->send(['code'=>$code]);
    }
}
