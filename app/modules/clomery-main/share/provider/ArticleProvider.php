<?php
namespace dxkite\clomery\main\provider;

use dxkite\support\view\PageData;
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

    public function __construct()
    {
        $this->article = new ArticleController('clomery');
    }
    
    /**
     * 写入文章
     *
     * @acl clomery.write:article
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
    
    /**
     * 获取文章列表
     *
     * @param integer|null $categoryId 当前选择的分类
     * @param integer $page 当前页
     * @param integer $count 页大小
     * @return PageData
     */
    public function getList(?int $categoryId =null, int $page=null, int $count=10):PageData
    {
        $userid = null;
        if (!\visitor()->isGuest()) {
            $userid = \get_user_id(); 
        }
        return $this->article->getList($userid,$categoryId,$page,$count);
    }

    /**
     * 发布文章
     *
     * @param integer $article
     * @return int
     */    
    public function post(int $article):int {
        return $this->article->update($article,[
            'status' => ArticleTable::STATUS_PUBLISH,
        ], get_user_id());
    }
}
