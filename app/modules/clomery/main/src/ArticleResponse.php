<?php
namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;

class ArticleResponse implements RequestProcessor {
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
        $article = $request->get('article');
        $data = $provider->getArticle($article);
        $page = $application->getTemplate('post', $request);
        $page->set('article', $data);
        return $page;
    }
}