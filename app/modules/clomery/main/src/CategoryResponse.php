<?php


namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;

class CategoryResponse implements RequestProcessor
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
        $data = $provider->getCategoryList();
        $page = $application->getTemplate('category', $request);
        if ($data === null) {
            $response->status(404);
            return '';
        }
        $page->set('title','分类列表');
        $page->set('page',$data->getPage());
        $page->set('categories', $data->getRows());
        return $page;
    }
}