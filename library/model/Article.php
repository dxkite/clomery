<?php
namespace model;

use Query;

class Article
{
    const TYPE_HTML=1;
    const TYPE_MD=0;

    const STATE_PUBLISH=1;// 发布
    const STATE_DRAFT=2;  // 草稿
    const STATE_DELETE=3; // 删除
    const STATE_VERIFY=4; // 待审核

    // 创建文章
    public function create(int $author, int $categroy, string $title, string $abstract, string $content, int $reply=1, int $type=self::TYPE_MD, int $state=self::STATE_VERIFY)
    {
        return Query::insert('article',
        [
            'author'=>$author,
            'categroy'=>$categroy,
            'title'=>$title,
            'abstract'=>$abstract,
            'content'=>$content,
            'create'=>time(),
            'update'=>time(),
            'type'=>$type,
            'allow_reply'=>$reply,
            'state'=>$state,
        ]);
    }

    // 删除文章
    public function update(int $id, int $author, int $categroy, string $title, string $abstract, string $content, int $reply=1, int $type=self::TYPE_MD, int $state=self::STATE_VERIFY)
    {
        return Query::update('article',
        [
            'author'=>$author,
            'categroy'=>$categroy,
            'title'=>$title,
            'abstract'=>$abstract,
            'content'=>$content,
            'create'=>time(),
            'update'=>time(),
            'type'=>$type,
            'allow_reply'=>$reply,
            'state'=>$state,
        ], ['id'=>$id]);
    }

    public function list(int $page=1, int $count=10)
    {
        return Query::where('article', ['id', 'categroy', 'title', 'abstract', 'update', 'allow_reply', 'view'], ['state'=>self::STATE_PUBLISH], [], [$page, $count])->fetchAll();
    }

    public function listSort(string $field, int $type=SORT_ASC, int $page=1, int $count=10)
    {
        if (!in_array($field, ['update', 'create', 'view'])) {
            $field='update';
        }
        $order=$type===SORT_ASC?'ASC':'DESC';
        return Query::where('article', ['id', 'categroy', 'title', 'abstract', 'update', 'allow_reply', 'view'], 'state =:state ORDER BY `'.$field.'` '.$order, ['state'=>self::STATE_PUBLISH], [$page, $count])->fetchAll();
    }

    public function setState(int $id, int $state)
    {
        return Query::update('article', ['state'=>$state], ['id'=>$id]);
    }

    public function count()
    {
        return Query::count('article');
    }

    public function setCategory(int $id, int $categroy)
    {
        try {
            Query::begin();
            Query::update('article', ['categroy'=>$categroy], ['id'=>$id]);
            Query::update('categroy', 'count = count +1', ['id'=>$categroy]);
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        return true;
    }

    public function addTag(int $article, string $name)
    {
        if (($tag=Tag::getId($name)) || ($tag=Tag::create($name))) {
            if (Query::insert('article_tag',' (`article`,`tag`) SELECT :article,:tag FROM DUAL WHERE NOT EXISTS (SELECT `article`,`tag` FROM `#{article_tag}` WHERE article=:article AND tag=:tag ) ', ['article'=>$article, 'tag'=>$tag])) {
                return Tag::countAdd($tag);
            }
            return true;
        }
        return false;
    }
}
