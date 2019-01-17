<?php
namespace dxkite\article\provider;

use dxkite\support\view\PageData;
use dxkite\content\parser\Content;
use dxkite\article\table\ArticleTable;
use dxkite\article\view\ArticleView;
use dxkite\article\controller\ArticleController;
use dxkite\article\controller\ArticleTagController;
use dxkite\article\controller\ArticleCategoryController;

class ArticleProvider
{
    /**
     * 文章控制器
     *
     * @var ArticleController
     */
    protected $article;
    /**
     * 文章分类
     *
     * @var ArticleCategoryController
     */
    protected $category;

    /**
     * 标签
     *
     * @var ArticleTagController
     */
    protected $tag;

    /**
     * 视图处理
     *
     * @var ArticleView
     */
    protected $view;

    public function __construct()
    {
        $this->article = new ArticleController('clomery');
        $this->category = new ArticleCategoryController('clomery');
        $this->tag = new ArticleTagController('clomery');
        $this->view = new ArticleView($this->article, $this->category, $this->tag);
    }
        
    /**
     * 写入文章
     *
     * @acl clomery:article.write:article
     * @param integer|null $id 文章ID/修改则填入
     * @param string $title 文章标题
     * @param string|null $slug 文章唯一标识
     * @param integer $category 文章分类
     * @param integer $cover 文章封面
     * @param array|null $tags
     * @param Content $excerpt 文章摘要
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
        ?array $tags= null,
        Content $excerpt,
        Content $content,
        ?int $modify=null,
        int $status=ArticleTable::STATUS_DRAFT
    ) :int {
        $articleId = $this->article->save($id, \get_user_id(), $title, $slug, $category, $cover, $excerpt, $content, $modify, $status);
        if (is_array($tags)) {
            $this->tag->addTags($articleId, $tags);
        }
        return $articleId;
    }
    
    /**
     * 获取文章列表
     *
     * @param integer|null $categoryId 当前选择的分类
     * @param integer|null $page 当前页
     * @param integer $count 页大小
     * @return PageData
     */
    public function getList(?int $categoryId =null, ?int $page=null, int $count=10):PageData
    {
        $userid = null;
        if (!\visitor()->isGuest()) {
            $userid = \get_user_id();
        }
        $page = $this->article->getList($userid, $categoryId, $page, $count);
        return $this->view->listView($page);
    }

    public function getListByTag(int $tagId, ?int $page=null, int $count=10):PageData
    {
        $userid = null;
        if (!\visitor()->isGuest()) {
            $userid = \get_user_id();
        }
        $article=[];
        $tagRefs = $this->tag->getRefByTag($tagId);
        if (is_array($tagRefs)) {
            foreach ($tagRefs as $value) {
                $articles[] = $value['ref'];
            }
        }
        $page = $this->article->getArticleListByIds($userid, $articles, $page, $count);
        return $this->view->listView($page);
    }

    /**
     * 获取分类列表
     *
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getCategoryList(?int $page=null, int $count=10):PageData
    {
        $page = $this->category->getList($page, $count);
        return $page;
    }


    /**
     * 获取标签列表
     *
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getTagList(?int $page=null, int $count=10):PageData
    {
        $page = $this->tag->getTags($page, $count);
        return $page;
    }
    
    /**
     * 发布文章
     *
     * @param integer $article
     * @return int
     */
    public function post(int $article):int
    {
        return $this->article->update($article, [
            'status' => ArticleTable::STATUS_PUBLISH,
        ], get_user_id());
    }

    /**
     * 删除文章
     *
     * @param integer $article 删除文章
     * @return integer
     */
    public function delete(int $article):int
    {
        return $this->article->delete($article, get_user_id());
    }
    
    /**
       * 搜索标题
       *
       * @param string $title 标题关键字
       * @param integer|null $user 指定用户
       * @param integer|null $category 指定分类
       * @param integer|null $page
       * @param integer $count
       * @return PageData 搜索结果页数据
       */
    public function search(string $title, ?int $user=null, ?int $category=null, ?int $page, int $count=10):PageData
    {
        $page = $this->article->search($title, $user, $category, $page, $count);
        return $this->view->listView($page);
    }

    /**
     * 获取文章信息
     *
     * @param integer $article
     * @return array|null
     */
    public function getArticle(int $article):?array
    {
        $userid = null;
        if (!\visitor()->isGuest()) {
            $userid = \get_user_id();
        }
        if (!session()->has('article.view.'.$article)) {
            $this->article->updateArticleViewCount($article);
            session()->set('article.view.'.$article, 1);
        }
        $article = $this->article->getArticle($userid, $article);
        return $this->view->article($article);
    }
}
