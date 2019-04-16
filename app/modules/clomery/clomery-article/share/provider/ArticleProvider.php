<?php
namespace clomery\article\provider;

use clomery\article\data\ArticleData;
use clomery\article\controller\ArticleController;



/**
 * 文章控制器
 *
 * 控制文章的内容处理
 */
class ArticleProvider
{
    
    /**
     * 文章数据
     *
     * @param ArticleData $data
     * @return integer
     */
    public function save(ArticleData $article):int
    {
        return (new ArticleController)->save($article);
    }

    public function list()
    {
        return (new ArticleController)->getList();
    }
}
