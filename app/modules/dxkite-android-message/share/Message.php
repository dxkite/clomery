<?php
namespace dxkite\android\message;

use dxkite\support\proxy\ProxyObject;

class Message extends ProxyObject
{
    public function pull()
    {
        $message = setting('androidMessage',false);
        if ($message && setting('androidMessageEnable',false)){
            return $message;
        }
        return false;
    }

    public function pullAd()
    {
        return setting('androidAds',false);
    }

    public function editPull(string $message,int $time=10000,string $url=null,string $color='#222222',string $bgColor='#EEEEEE'){
        $message = [
            'message' => $message,
            'url'=>$url,
            'time'=>$time,
            'color'=>$color,
            'backgroundColor'=> $bgColor,
            'touchable' => !empty($url),
        ];
        return setting_val('androidMessage',$message);
    }

    public function editAds(string $image,string $url){
        $ads=[
            'image'=>$image,
            'url'=>$url,
        ];
        return setting_val('androidAds',$ads);
    }

    public function enableMessage(bool $enable=true) {
        return setting_val('androidMessageEnable',$enable);
    }
}
