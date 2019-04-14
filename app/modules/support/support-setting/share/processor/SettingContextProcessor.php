<?php
namespace support\setting\processor;

use suda\framework\Request;
use suda\framework\Response;
use support\setting\Context;
use support\setting\Visitor;
use support\session\UserSession;
use suda\application\Application;
use support\setting\provider\VisitorProvider;
use suda\application\processor\RequestProcessor;

/**
 * 设置环境状态
 */
class SettingContextProcessor implements RequestProcessor
{
    public function onRequest(Application $application, Request $request, Response $response)
    {
        $context = new Context($application, $request, $response);
        $session = UserSession::createFromRequest($request, 'system');
        $vp = new VisitorProvider;
        $context->setVisitor($vp->getVisitor($session));
        return $context;
    }
}
