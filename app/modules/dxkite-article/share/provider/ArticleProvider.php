<?php
namespace dxkite\article\provider;

use dxkite\content\parser\Content;
use dxkite\article\table\ArticleTable;
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
     * @param integer|null $id 文章ID/修改则填入
     * @param string $title 文章标题
     * @param string|null $slug 文章唯一标识
     * @param integer $category 文章分类
     * @param integer $cover 文章封面
     * @param Content $abstract 文章摘要
     * @param Content $content 文章内容
     * @param integer|null $modify 文章修改时间
     * @param integer $status 文章状态
     * @return integer 文章id
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
