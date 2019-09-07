<?php

namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use clomery\main\table\ArticleTable;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;
use support\visitor\provider\UserSessionAwareProvider;

class ArticleResponse extends UserSessionAwareProvider implements RequestProcessor
{

    /**
     * @var string
     */
    protected $group = 'clomery';

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
        $article = $request->get('article');
        $provider->setContext($application, $request, $response);
        $data = $provider->getArticle($article);

        // 没有文章
        if ($data === null) {
            $response->status(404);
            return '';
        }

        // 文章不是显示状态
        if ($data['status'] != ArticleTable::PUBLISH) {
            $response->status(403);
            return '';
        }

        $page = $application->getTemplate('post', $request);
        $page->set('article', $data);
        $page->set('title', $data['title']);
        return $page;
    }
}