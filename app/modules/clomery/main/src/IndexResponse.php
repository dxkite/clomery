<?php


namespace clomery\main\response;




use clomery\main\provider\ArticleProvider;
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
     * @throws \suda\database\exception\SQLException
     */
    public function onRequest(Application $application, Request $request, Response $response)
    {
        $provider = new ArticleProvider();
        $page = $request->get('page', 1);
        $data = $provider->getArticleList(null, null,null, $page, 10);
        $page = $application->getTemplate('index', $request);
        $page->set('articles', $data->getRows());
        $page->set('page',$data->getPage());
        return $page;
    }
}