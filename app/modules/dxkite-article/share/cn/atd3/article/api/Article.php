<?php
namespace cn\atd3\article\api;

use cn\atd3\proxy\Proxy;
use cn\atd3\proxy\ProxyObject;
use cn\atd3\article\proxyobject\ArticleProxy;
use cn\atd3\article\proxyobject\CategoryProxy;
use cn\atd3\article\proxyobject\TagProxy;
use cn\atd3\user\UserProxy;
use cn\atd3\visitor\Context;

class Article extends ProxyObject
{
    protected $articleDb;
    protected $userDb;
    protected $categoryDb;
    protected $tagDb;

    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->articleDb=new Proxy(new ArticleProxy($context));
        $this->userDb=new Proxy(new UserProxy($context));
        $this->categoryDb=new Proxy(new CategoryProxy($context));
        $this->tagDb=new Proxy(new TagProxy($context));
    }

    public function authorInfo(int $id)
    {
        return [
            'id'=>$id,
            'name'=>$this->userDb->id2name($id),
            'link'=>u('article:author', $id),
            'avatar'=>u('user:avatar', $id),
        ];
    }
    
    public function categoryInfo(int $cateid)
    {
        return [
            'id'=>intval($cateid),
            'name'=>self::cate2name($cateid),
            'link'=>u('article:category_list', $cateid),
        ];
    }
    
    public function tagsInfo(array $tagid)
    {
        $tags=$this->tagDb->getTagsName($tagid);
        $result=[];
        foreach ($tags as $id=>$name) {
            $result=[
                'id'=>intval($id),
                'name'=>$name,
                'link'=>u('article:tag_list', $id),
            ];
        }
        return $result;
    }
    
    
    public function getArticle(int $aritlce)
    {
        if ($article=$this->articleDb->getArticleById($aritlce)) {
            return self::parseArticle($article);
        }
        return null;
    }

    public function articleTags(int $aid)
    {
        $ids=$this->tagDb->getTags($aid);
        return count($ids)?self::tagsInfo($ids):null;
    }

    public function cate2name(int $id)
    {
        return $this->categoryDb->id2name($id)??__('默认分类');
    }

    /**
     * 获取文章列表，显示详细信息
     * 
     * @param int $page
     * @param int $rows
     * @return void
     */
    public function getArticleList(int $page, int $rows=10)
    {
        // TODO set field
        $list=$this->articleDb->getList($page, $rows);
        return is_array($list)?self::parseList($list):null;
    }
    
    /**
     * 按分类列出文章
     *
     * @param int $cateid
     * @param int $page
     * @param int $rows
     * @return void
     */
    public function getListByCategoryId(int $cateid, int $page, int $rows=10)
    {
        $list=$this->categoryDb->getArticleById($cateid, $page, $rows);
        return is_array($list)?self::parseList($list):null;
    }
    
    /**
     * 按标签列出文章
     *
     * @param int $tag
     * @param int $page
     * @param int $rows
     * @return void
     */
    public function getListByTagId(int $tag, int $page, int $rows=10)
    {
        $list=$this->tagDb->getArticleById($tag, $page, $rows);
        return is_array($list)?self::parseList($list):null;
    }

    public function getCategorys()
    {
        $list=$this->categoryDb->getList();
        if (is_array($list)) {
            $catelist=[];
            foreach ($list as $index=> $item) {
                $catelist[$item['id']]=$item;
                $catelist[$item['id']]['id']=intval($item['id']);
                $catelist[$item['id']]['link']=u('article:category_list', $item['id']);
            }
            foreach ($catelist as $index=> $item) {
                if ($item['parent']!=0) {
                    $catelist[$item['parent']]['childs'][]=&$catelist[$index];
                }
            }
            foreach ($catelist as $index=> $item) {
                if ($item['parent']!=0) {
                    unset($catelist[$index]);
                }
            }
            sort($catelist);
            return $catelist;
        }
        return null;
    }

    public function getTags()
    {
        $list=$this->tagDb->getList();
        if (is_array($list)) {
            foreach ($list as $index=> $item) {
                $list[$index]['id']=intval($item['id']);
                $list[$index]['link']=u('article:tag_list', $item['id']);
            }
            return $list;
        }
        return null;
    }
    
    public function getRootCategorys()
    {
        $list=$this->categoryDb->getRootList();
        if (is_array($list)) {
            foreach ($list as $index=> $item) {
                $list[$index]['id']=intval($item['id']);
                $list[$index]['link']=u('article:category_list', $item['id']);
            }
            return $list;
        }
        return null;
    }

    protected function parseArticle(array $article)
    {
        $id=intval($article['id']);
        // 作者
        $article['author']=self::authorInfo($article['user']);
        // 链接
        $article['link']=u('article:read', $id);
        // 分类
        $cateid=$article['category'];
        $article['category'] =self::categoryInfo($cateid);
        // 编辑时间
        $article['modify']=[
            'unix'=>intval($article['modify']),
            'time'=>date(setting('date_format', 'Y-m-d H:i:s'), $article['modify']),
        ];
        // 创建时间
        $article['create']=[
            'unix'=>intval($article['create']),
            'time'=>date(setting('date_format', 'Y-m-d H:i:s'), $article['create']),
        ];
        $article['tag']=self::articleTags($id);
        $article['cover']=u('article:cover', $article['cover']);
        return $article;
    }

    protected function parseList(array $list)
    {
        foreach ($list as $index=>$item) {
            $list[$index]=self::parseArticle($item);
        }
        return $list;
    }
}
