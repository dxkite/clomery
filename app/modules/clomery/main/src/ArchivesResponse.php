<?php


namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;

class ArchivesResponse implements RequestProcessor
{
    /**
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @throws \suda\database\exception\SQLException
     * @throws \ReflectionException
     */
    public function onRequest(Application $application, Request $request, Response $response)
    {
        $provider = new ArticleProvider();
        $provider->setContext($application, $request, $response);
        $data = $provider->getArchives();
        $page = $application->getTemplate('archive', $request);
        $page->set('title','文章归档');
        $page->set('page',$data->getPage());
        $page->set('archives', $data->getRows());
        return $page;
    }
}