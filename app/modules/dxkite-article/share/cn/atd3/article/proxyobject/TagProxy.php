<?php
namespace cn\atd3\article\proxyobject;

use cn\atd3\proxy\ProxyObject;
use cn\atd3\article\dao\TagsDAO;
use cn\atd3\article\dao\TagDAO;
use cn\atd3\article\dao\ArticleDAO;
use cn\atd3\visitor\Context;

class TagProxy extends ProxyObject
{
    protected $tagDao;
    protected $tagReferDao;
    
    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->tagDao=new TagsDAO;
        $this->tagReferDao=new TagDAO;
    }
    
    /**
     * 获取标签名
     *
     * @param array $tagsid
     * @return void
     */
    public function getTagsName(array $tagsid)
    {
        $names=$this->tagDao->ids2name($tagsid);
        $result=[];
        foreach ($names as $item) {
            $result[$item['id']]=$item['name'];
        }
        return $result;
    }

    /**
     * 根据标签获取文章
     *
     * @param string $tag
     * @return void
     */
    public function getArticleList(string $tag, int $page, int $rows=10)
    {
        if ($id=$this->tagDao->getId($tag)) {
            if (is_null($page)) {
                return $this->getArticleListByTagId($id);
            } else {
                return $this->getArticleListByTagId($id, $page, $rows);
            }
        }
        return null;
    }

    /**
     * 根据标签Id获取文章
     *
     * @param string $tag
     * @return void
     */
    public function getArticleById(int $tag, int $page=null, int $rows=10)
    {
        if (is_null($page)) {
            $get=$this->tagReferDao->getRefByTag($tag);
        } else {
            $get=$this->tagReferDao->getRefByTag($tag, $page, $rows);
        }
        
        $aids=[];
        foreach ($get as $item) {
            $aids[]=$item['ref'];
        }
        if (count($aids)) {
            return (new ArticleDAO)->getArticleListByIds($aids);
        }
        return null;
    }

    /**
     * 添加TAG
     * @acl add_tag
     * @param string $name
     * @return void
     */
    public function add(string $name)
    {
        return $this->tagDao->add($this->getUserId(), $name);
    }

    /**
     * 获取全部标签列表
     *
     * @return void
     */
    public function getList()
    {
        return $this->tagDao->setWants(['id','name','time'])->list();
    }

    /**
     * 设置文章标签
     *
     * @param int $aid
     * @param array $tags
     * @return void
     */
    public function setTags(int $aid, array $tags)
    {
        return $this->tagDao->setTags($this->getUserId(), $aid, $tags);
    }

    /**
     * 获取文章标签
     *
     * @param int $aid
     * @return void
     */
    public function getTags(int $aid)
    {
        return $this->tagDao->getTags($aid);
    }
}
