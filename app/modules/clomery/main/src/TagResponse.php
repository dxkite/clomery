<?php


namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;

class TagResponse implements RequestProcessor
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
        $data = $provider->getTagList();
        if ($data === null) {
            $response->status(404);
            return '';
        }
        $page = $application->getTemplate('tag', $request);
        $page->set('title','标签列表');
        $page->set('page',$data->getPage());
        $page->set('tags', $data->getRows());
        return $page;
    }
}