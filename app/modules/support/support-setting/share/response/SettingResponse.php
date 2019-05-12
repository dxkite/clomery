<?php
namespace support\setting\response;

use suda\framework\Request;
use support\setting\MenuTree;
use suda\application\template\RawTemplate;
use support\setting\response\SignedResponse;
use support\openmethod\exception\PermissionException;

abstract class SettingResponse extends SignedResponse
{
    public function onAccessVisit(Request $request)
    {
        $visitor = $this->context->getVisitor();
        if ($visitor->canAccess([$this,'onSettingVisit'])) {
            try {
                return $this->onSettingVisit($request);
            } catch (PermissionException $e) {
                return $this->onDeny($request);
            }
        } else {
            return $this->onDeny($request);
        }
    }

    abstract public function onSettingVisit(Request $request);
}
