<?php


namespace clomery\main\response;




use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;

class IndexResponse implements RequestProcessor
{

    /**
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function onRequest(Application $application, Request $request, Response $response)
    {
        return $application->getTemplate('index', $request);
    }
}