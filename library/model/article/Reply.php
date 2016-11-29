<?php
namespace model\article;

use Query;
use Request;
use model\Article;

class Reply
{
    const STATE_DELETE =1;
    const STATE_VERIFY =2;
    const STATE_PUBLISH=3;

    // 回复评论
    public function create(int $comment, int $author, string $text,int $state=self::STATE_VERIFY)
    {
        return Query::insert('article_reply', [ 'comment'=>$comment, 'author'=>$author, 'text'=>$text, 'time'=>time(), 'ip'=>Request::ip(), 'state'=>$state]) && Comment::replyCount($comment);
    }

    // 回复回复
    public function reply(int $id, int $comment, int $author, string $text,int $state=self::STATE_VERIFY)
    {
        return Query::insert('article_reply', [ 'reply'=>$id,'comment'=>$comment,  'author'=>$author, 'text'=>$text, 'time'=>time(), 'ip'=>Request::ip(), 'state'=>$state]);
    }

    // 删除回复
    public function delete(int $id)
    {
        return Query::update('article_reply', ['state'=>self::STATE_DELETE], ['id'=>$id]);
    }

    // 删除评论
    public function list(int $comment, int $page=1, int $count=10)
    {
        return Query::where('article_reply', ['id', 'reply', 'comment', 'author', 'text', 'time', 'ip', 'state'], [ 'comment'=>$comment ], [], [$page, $count])->fetchAll();
    }
    
    public function listAfter(int $id,int $count=10){
        return Query::where('article_reply', ['id', 'reply', 'comment', 'author', 'text', 'time', 'ip', 'state'], ' id >:id LIMIT '.intval($count) ,[ 'id'=>$id ])->fetchAll();
    }
}
