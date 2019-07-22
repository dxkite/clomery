<?php

namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;


class CategoryArticleResponse implements RequestProcessor
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
        $category = $request->get('category');
        $categoryData = $provider->getCategory($category);
        if ($categoryData === null) {
            $response->status(404);
            return '';
        }
        $data = $provider->getArticleList($search, $categoryData['id'], null, $page, 10, $field, $order);
        $page = $application->getTemplate('article-category', $request);
        $page->set('category', $categoryData);
        $page->set('title', '分类 ' . $categoryData['name']);
        $page->set('articles', $data->getRows());
        $page->set('page', $data->getPage());
        return $page;
    }
}