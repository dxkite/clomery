<?php
namespace clomery\main\response;


use clomery\main\provider\ArticleProvider;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\framework\Request;
use suda\framework\Response;
use support\visitor\provider\UserSessionAwareProvider;

class ArticleResponse extends  UserSessionAwareProvider implements RequestProcessor {

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
        $data = $provider->getArticle($article);
        $page = $application->getTemplate('post', $request);
        $page->set('article', $data);
        $this->setContext($application,$request,$response);
        $session = $this->getContext()->getSession();
        if ($session->has('read_'.$data['id']) === false) {
            $provider->getController()->pushCountView($data['id'], 1);
            $session->set('read_'.$data['id'], 1);
        }
        return $page;
    }
}