<?php
namespace cn\atd3\article\proxyobject;

use cn\atd3\proxy\ProxyObject;
use cn\atd3\article\dao\CategoryDAO;
use cn\atd3\article\dao\ArticleDAO;
use cn\atd3\visitor\Context;

class CategoryProxy extends ProxyObject
{
    protected $categoryDAO;

    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->categoryDAO=new CategoryDAO;
    }
    
    
    /**
     * 根据主题获取文章
     *
     * @param string $category
     * @return void
     */
    public function getArticleList(string $category, int $page, int $rows=10)
    {
        $id=$this->categoryDAO->name2id($category);
        if ($id!==false) {
            return (new ArticleDAO)->getListByCategory($id, $page, $rows);
        }
        return false;
    }

    /**
     * 根据主题获取文章
     *
     * @param string $cateid
     * @return void
     */
    public function getArticleById(int $cateid, int $page, int $rows=10)
    {
        return (new ArticleDAO)->getListByCategory($cateid, $page, $rows);
    }
    
    /**
     * 设置分类
     *
     * @param int $aid
     * @param int $cateid
     * @return void
     */
    public function setCategory(int $aid, int $cateid)
    {
        return $this->categoryDAO->setCategory($this->getUserId(), $aid, $cateid);
    }
    
    /**
     * 创建分类
     * @acl add_category
     *
     * @param string $name
     * @param string $slug
     * @param int $parent
     * @return void
     */
    public function add(string $name, string $slug, int $parent=0)
    {
        return $this->categoryDAO->add($this->getUserId(), $name, $slug, $parent);
    }

    /**
     * 获取分类名
     *
     * @param array $category_ids
     * @return void
     */
    public function getNameByIds(array $category_ids)
    {
        return $this->categoryDAO->ids2name($category_ids);
    }
    
    /**
     * 获取分类名
     *
     * @param array $category_ids
     * @return void
     */
    public function id2name(int $id)
    {
        return $this->getNameByIds([$id])[$id]??null;
    }
    
    /**
     * 获取分类列表
     *
     * @return void
     */
    public function getList()
    {
        return $this->categoryDAO->setWants(['id','name','slug','count','parent'])->list();
    }
    
    /**
     * 获取分类列表
     *
     * @return void
     */
    public function getRootList()
    {
        return $this->categoryDAO->setWants(['id','name','slug','count','parent'])->listWhere(['parent'=>0]);
    }

    /**
     * @acl delete_category
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        return $this->categoryDAO->deleteByPrimarykey($id);
    }

    /**
     * 获取分类列表
     *
     * @return void
     */
    public function count()
    {
        return $this->categoryDAO->count();
    }

    /**
     * 编辑分类
     * @acl edit_category
     * @return void
     */
    public function update(int $id, array $sets)
    {
        return $this->categoryDAO->updateByPrimaryKey($id, $sets);
    }

    /**
     * 编辑分类
     * @acl edit_category
     * @return void
     */
    public function get(int $id)
    {
        return $this->categoryDAO->getByPrimaryKey($id);
    }
}
