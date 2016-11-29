<?php
namespace model\article;

use Query;
use Request;
use model\Article;

class Comment
{
    const STATE_DELETE =1;
    const STATE_VERIFY =2;
    const STATE_PUBLISH=3;

    public function create(int $article, int $author, string $text, int $state=self::STATE_PUBLISH)
    {
        return Query::insert('article_comment',
         '(`article`,`author`,`count`,`text`,`time`,`ip`,`state`) SELECT :article,:author,count(`id`) + 1 ,:text,:time,:ip,:state FROM `#{article_comment}` WHERE `article`= :article ',
          ['article'=>$article, 'author'=>$author, 'text'=>$text, 'time'=>time(), 'ip'=>Request::ip(), 'state'=>$state]) && Article::replyCount($article);
    }

    public function delete(int $id)
    {
        return Query::update('article_comment', ['state'=>self::STATE_DELETE],['id'=>$id]);
    }

    public function list(int $article,string $field='count',int $type=SORT_ASC,int $page=1, int $count=10)
    {
        if (!in_array($field, ['reply', 'time', 'count'])) {
            $field='count';
        }
        $order=$type===SORT_ASC?'ASC':'DESC';
        return Query::where('article_comment', ['id', 'count', 'reply', 'author', 'text', 'time'], '`article`=:article AND `state`=:state ORDER BY `'.$field.'` '.$order ,['state'=>self::STATE_PUBLISH,'article'=>$article], [$page, $count])->fetchAll();
    }

    public function replyCount(int $id){
        return Query::update('article_comment', ' reply = reply + 1',['id'=>$id]);
    }
    
    public function listAfter(int $article,int $id=1, string $field='count',int $type=SORT_ASC,int $count=10)
    {
        if (!in_array($field, ['reply', 'time', 'count'])) {
            $field='count';
        }
        $order=$type===SORT_ASC?'ASC':'DESC';
        return Query::where('article_comment', ['id', 'count', 'reply', 'author', 'text', 'time'], '`article`=:article AND `state`=:state AND `id` > :id ORDER BY `'.$field.'` '.$order ,['state'=>self::STATE_PUBLISH,'article'=>$article,'id'=>$id], [1, $count])->fetchAll();
    }
}
