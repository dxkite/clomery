<?php


namespace dxkite\goget\processor;


use suda\framework\Request;
use suda\framework\Response;
use suda\application\Application;
use dxkite\goget\event\GoGetEvent;
use suda\application\processor\RequestProcessor;
use suda\application\processor\RequestChainProcessor;

class GoGetProcessor implements RequestChainProcessor
{
    public function onRequest(Application $application, Request $request, Response $response, RequestProcessor $next)
    {
        if ($request->get('go-get') == 1) {
            return GoGetEvent::getGoGet($application, $request);
        }
        return $next->onRequest($application, $request, $response);
    }
}