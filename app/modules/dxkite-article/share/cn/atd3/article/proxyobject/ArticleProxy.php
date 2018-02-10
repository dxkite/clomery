<?php
namespace cn\atd3\article\proxyobject;

use cn\atd3\proxy\ProxyObject;
use cn\atd3\article\dao\ArticleDAO;
use cn\atd3\visitor\Context;
use cn\atd3\upload\UploadProxy;
use cn\atd3\upload\File;

class ArticleProxy extends ProxyObject
{
    const DELETE=0;     // 删除
    const DRAFT=1;      // 草稿
    const PUBLISH=3;    // 发布
    const FILE_USAGE_COVER='article_cover';

    protected $articleDao;

    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->articleDao=new ArticleDAO;
    }

    /**
     * 获取文章列表
     *
     * @param int $page
     * @param int $rows
     * @return void
     */
    public function getList(int $page, int $rows, string $order='modify', int $asc=1)
    {
        return $this->articleDao->order($order, $asc)->getList($page, $rows);
    }

    /**
     * 根据Id获取文章内容
     *
     * @param int $id
     * @return void
     */
    public function getArticleById(int $id)
    {
        $session='article_'.$id.'_readed';
        if ($this->getContext()->getVisitor()->isGuest()) {
            $content=$this->articleDao->getArticle($id);
        } else {
            $content=$this->articleDao->getArticle($id, $this->getUserId());
        }
        if ($content && !$this->getContext()->getSession($session, false)) {
            $this->articleDao->viewPlus($id);
            $this->getContext()->setSession($session, true);
        }
        return $content;
    }

    /**
     * 删除文章
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        if ($this->hasPermission('delete_article')) {
            return $this->articleDao->deleteByPrimaryKey($id);
        }
        return $this->articleDao->deleteByUser($this->getUserId(), $id);
    }
     
    /**
     * 创建文章
     * @acl add_article
     * @param string $title
     * @param string $content
     * @param int $category
     * @param int $type
     * @param int $status
     * @return void
     */
    public function create(string $title, string $content, int $category, int $type=ArticleDAO::TYPE_PLAIN, int $status=ArticleDAO::STATUS_DRAFT)
    {
        return $this->articleDao->create($this->getUserId(), $title, $content, $category, $type, $status);
    }

    /**
     * 设置文章状态
     *
     * @param int $article
     * @param int $status
     * @return void
     */
    public function setStatus(int $article, int $status)
    {
        return $this->articleDao->setStatus($article, $status, $this->getUserId());
    }

    /**
     * 设置摘要
     *
     * @param int $article
     * @param string $abstract
     * @return void
     */
    public function setAbstract(int $article, string $abstract)
    {
        return $this->articleDao->setAbstract($article, $abstract, $this->getUserId());
    }

    /**
     * 设置封面
     *
     * @param int $article
     * @param File $cover
     * @return void
     */
    public function setCover(int $article, File $cover)
    {
        $upload=new UploadProxy($this->getContext());
        $coverid=$upload->save($cover, self::FILE_USAGE_COVER, $upload::STATE_PUBLISH, $upload::FILE_PUBLIC)->getId();
        if ($coverid) {
            $id=$this->articleDao->getCover($article, $this->getUserId());
            $result=$this->articleDao->setCover($article, $coverid, $this->getUserId())>0?true:false;
            if ($result && $id) {
                $upload->delete($id);
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 
     * @paramSource get,json
     * @param int $article
     * @return void
     */
    public function getCover(int $article)
    {
        $upload=new UploadProxy($this->getContext());
        $id=$this->articleDao->getCover($article);
        if ($id) {
            return $upload->getPublicFile($id);
        }
        return false;
    }
 
    /**
     * 获取文章列表
     * @acl list_article
     * @param int $page
     * @param int $rows
     * @return void
     */
    public function list(int $page, int $rows)
    {
        return $this->articleDao->list($page, $rows);
    }

    /**
     * 获取文章
     * @acl edit_article
     * @param int $page
     * @param int $rows
     * @return void
     */
    public function get(int $id)
    {
        return $this->articleDao->getByPrimaryKey($id);
    }

    /**
     * 编辑文章
     * @return void
     */
    public function edit(int $id, array $sets)
    {
        if ($this->hasPermission('edit_article')) {
            return $this->articleDao->edit($id, $sets);
        }
        return $this->articleDao->edit($id, $sets, $this->getUserId());
    }
}
