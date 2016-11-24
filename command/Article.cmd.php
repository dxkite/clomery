<?php

class Article
{
    public function getById(Request $rq)
    {
        echo 'Article - '.$rq->get('id');
        echo Router::url('view-article', ['id'=>12]);
        Event::listen('Page:test', function () {
            echo 'PageHock';
        });
        Helper::mailer();
        Cookie::set('Dxkite', 'IsME!', 100000);

        return ['title'=>'DXkite'];
    }
    public function jsonReturn()
    {
        return ['hello'=>'world'];
    }
    public function id()
    {
        $page=new Page();
        $page->setStatus(400);
        $page->setContent('Bad Request!');
        return $page;
    }
}
