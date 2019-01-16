<?php
namespace dxkite\article\provider;

use dxkite\article\controller\ArticleController;

class ArticleProvider
{
    /**
     * 文章控制器
     *
     * @var ArticleController
     */
    protected $article;

    public function __construct(string $prefix)
    {
        $this->article = new ArticleController($prefix);
    }
    
    /**
     * 写入文章
     *
     * @acl dxkite:article.write:article
     * @param integer|null $id
     * @param string $title
     * @param string|null $slug
     * @param integer $category
     * @param integer $cover
     * @param Content $abstract
     * @param Content $content
     * @param integer|null $modify
     * @param integer $status
     * @return integer
     */
    public function save(
        ?int $id =null,
        string $title,
        ?string $slug=null,
        int $category=0,
        int $cover= 0,

        Content $abstract,
        Content $content,
        
        ?int $modify=null,
        int $status=ArticleTable::STATUS_DRAFT
    ) :int {
        return $this->article->save($id, \get_user_id(), $title, $slug, $category, $cover, $abstract, $content, $modify, $status);
    }
    
}
