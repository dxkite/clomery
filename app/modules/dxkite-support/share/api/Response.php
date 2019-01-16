<?php
namespace dxkite\support\api;

use dxkite\support\visitor\response\CallableResponse;
use suda\core\route\Mapping;

class Response extends CallableResponse
{
    public function __default()
    {
        $param=Mapping::$current->getParam();
        $cors = app()->getConfig('api/access') ?? app()->getModuleConfig($param['module'], 'api/access');
        if (is_array($cors)) {
            foreach ($cors as $header => $value) {
                if (is_array($value)) {
                    self::addHeader('Access-Control-'. $header, implode(',', $value));
                } else {
                    self::addHeader('Access-Control-'. $header, $value);
                }
            }
        }
        if (strtoupper(request()->getMethod()) == 'OPTIONS') {
            // 跨域头检测
        } elseif (hook()->execIf('support:api:access:deny', [$this], true)) {
            $this->onDeny($this->getContext());
        } else {
            return parent::__default();
        }
    }

    public function getExportMethods($class=null)
    {
        $param=Mapping::$current->getParam();
        return parent::getExportMethods($param['proxyClass']);
    }
}
