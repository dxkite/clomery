<?php
namespace dxkite\android\collection;
use dxkite\support\proxy\ProxyObject;

class Collection extends ProxyObject
{ 
    /**
     * 统计Android启动信息
     * 启动时调用该接口
     *
     * @param string $name 设备名
     * @param string $deviceId 设备ID
     * @param string $packageName 程序包名
     * @param string $activiy 活动
     * @return integer
     */
    public function android(string $name, string $deviceId, string $packageName,string $activiy):int
    {
        $data=[
            'name'=>$name,
            'device'=>'Android/'.$deviceId,
            'ip'=>request()->ip(),
            'content'=>$activiy,
            'refer'=>$packageName,
            'time'=>time()
        ];
        if ($id=$this->getContext()->getVisitor()->getId()) {
            $data['user']=$id;
        }
        return table('collection')->insert($data);
    }
}
