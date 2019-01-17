<?php
namespace dxkite\article\provider;

use suda\archive\Table;
use dxkite\support\view\PageData;
use dxkite\article\controller\ArticleTagController;



class ArticleTagProvider
{

    /**
     * 标签
     *
     * @var ArticleTagController
     */
    protected $tag;


    public function __construct()
    {
        $this->tag = new ArticleTagController('clomery');
    }

    /**
     * 获取标签列表
     *
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getList(?int $page=null, int $count=10):PageData
    {
        $page = $this->tag->getTags($page, $count);
        return $page;
    }

    /**
     * 根据标签名获取Id
     *
     * @param string $name
     * @return array|null
     */
    public function getTagByName(string $name):?array {
        return $this->tag->getTagByName($name);
    }

    /**
     * 根据标签获取文章列表
     *
     * @param integer $tag
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getArticleByTag(int $tag, ?int $page=null, int $count=10):PageData {
        $userid = null;
        if (!\visitor()->isGuest()) {
            $userid = \get_user_id();
        }
        return $this->tag->getArticleByTag($userid,$tag,'modify', Table::ORDER_DESC,$page,$count);
    }
}
