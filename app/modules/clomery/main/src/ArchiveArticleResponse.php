<?php


namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;

class ArchiveArticleResponse implements RequestProcessor
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
        $date = $request->get('date', 0);
        $dateShow = \date_create_from_format('Y-m', $date)->format($application->_('Y年m月d日'));
        $data = $provider->getArticleListByDate($date, $page);
        $page = $application->getTemplate('article-archive', $request);
        $page->set('title',$application->_('归档 ') . $dateShow);
        $page->set('articles', $data->getRows());
        $page->set('date',['show'=>$dateShow, 'raw'=>$date]);
        $page->set('page',$data->getPage());
        return $page;
    }
}