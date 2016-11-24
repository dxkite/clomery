<?php

class Article
{
    public function getById(Request $rq)
    {
        echo 'Article - '.$rq->get('id');
    }
    public function jsonReturn()
    {
        return ['hello'=>'world'];
    }
}
