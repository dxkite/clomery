<?php
namespace dxkite\android\message;

use dxkite\support\proxy\ProxyObject;
use  dxkite\support\file\Media;
use dxkite\support\file\File;

class Message extends ProxyObject
{
    public static function pull()
    {
        $message = setting('androidMessage', false);
        if ($message && setting('androidMessageEnable', false)) {
            return $message;
        }
        return false;
    }

    public static function pullAd()
    {
        return setting('androidAds', false);
    }

    public static function editPull(string $message, int $time=10000, string $url=null, string $color='#222222', string $bgColor='#EEEEEE')
    {
        $message = [
            'message' => $message,
            'url'=>$url,
            'time'=>$time,
            'color'=>$color,
            'backgroundColor'=> $bgColor,
            'touchable' => !empty($url),
            'create'=>time(),
        ];
        return setting_set('androidMessage', $message);
    }

    public static function editAds(File $image, string $url)
    {
        if ($old=setting('androidAds')) {
            if (isset($old['imageId'])) {
                Media::delete($old['imageId']);
            }
        }
        $upload=Media::saveFile($image);
        if ($upload->getId()>0) {
            $ads=[
                'image'=>  u('support:upload', ['id'=>$upload->getId()]),
                'imageId'=> $upload->getId(),
                'url'=>$url,
            ];
            return setting_set('androidAds', $ads);
        }
        return false;
    }

    public static function enableMessage(bool $enable=true)
    {
        return setting_set('androidMessageEnable', $enable);
    }
}
