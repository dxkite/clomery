<?php
namespace clomery\article\provider;

use clomery\article\DataUnit;
use clomery\article\data\TagData;
use clomery\article\data\ArticleData;
use clomery\article\data\CategoryData;
use clomery\article\data\TagRelateData;
use clomery\article\controller\ArticleController;
use dxkite\openuser\provider\VisitorAwareProvider;
use support\openmethod\FrameworkContextAwareInterface;


/**
 * 文章控制器
 *
 * 控制文章的内容处理
 */
class ArticleProvider extends VisitorAwareProvider
{
    
    /**
     * 文章数据
     *
     * @param \clomery\article\data\ArticleData $article
     * @param array $tags
     * @return string
     */
    public function save(ArticleData $article, array $tags):string
    {
        // $dataunit = new DataUnit;
        // $dataunit->init(TagData::class, $this->application);
        return (new ArticleController)->save($article, $tags, null, true);
    }

    public function list()
    {
        return (new ArticleController)->getList();
    }
}
