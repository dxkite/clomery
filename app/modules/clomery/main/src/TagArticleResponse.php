<?php

namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;


class TagArticleResponse implements RequestProcessor
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
        $field = $request->get('sort', 0);
        $order = $request->get('order', 0);
        $search = $request->get('search', null);
        $tag = $request->get('tag');
        $tagData = $provider->getTag($tag);

        if ($tagData === null) {
            $response->status(404);
            return '';
        }

        $data = $provider->getArticleList($search, null, [$tagData['id']], $page, 10, $field, $order);
        $page = $application->getTemplate('article-tag', $request);
        $page->set('tag', $tagData);
        $page->set('title', '标签 ' . $tagData['name']);
        $page->set('articles', $data->getRows());
        $page->set('page', $data->getPage());
        return $page;
    }
}