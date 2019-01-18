<?php
namespace dxkite\tags\controller;

use suda\tool\Command;
use suda\archive\Table;
use dxkite\tags\table\TagTable;
use dxkite\tags\table\TagsTable;
use dxkite\support\view\PageData;
use dxkite\support\view\TablePager;

class TagController
{
    /**
     * 标签表
     *
     * @var TagsTable
     */
    protected $tagsTable;
    
    /**
     * 标签关系表
     *
     * @var TagTable
     */
    protected $tagTable;
    /**
     * 目标表
     *
     * @var Table
     */
    protected $target;

    public function __construct(string $target = null)
    {
        $this->target = Command::newClassInstance($target);
        $this->tagsTable = new TagsTable($this->target);
        $this->tagTable = new TagTable($this->target);
    }

    /**
     * 保存标签
     *
     * @param integer $user 创建用户
     * @param string $name 创建标签
     * @param boolean $insertIfNotExists 不存在是否插入
     * @return integer
     */
    public function save(int $user, string $name, bool $insertIfNotExists=true):int
    {
        if ($tag = $this->getTagByName($name)) {
            return $tag['id'];
        } elseif ($insertIfNotExists) {
            return $this->tagsTable->insert(['name'=>$name,'user'=> $user,'time'=>time()]);
        }
    }

    public function setTags(int $target, array $tags)
    {
        if ($this->target->select(['id'], ['id'=>$target])->fetch()) {
            return $this->bindTags($target, $tags);
        }
        return false;
    }

    public function exist(string $name)
    {
        return $this->getTagId($name)?true:false;
    }
 
    public function getTags(?int $page=null, int $row=10)
    {
        return TablePager::listWhere($this->tagsTable->setWants(['id','name','count']), '1', [], $page, $row);
    }

    public function getTagByIds(array $ids)
    {
        return $this->tagsTable->select(['id','name'], ['id'=>$ids])->fetchAll();
    }

    public function getTagByName(string $name):?array
    {
        return $this->tagsTable->select(['id','name','count'], ' LOWER(name)=LOWER(:name) ', ['name'=>$name])->fetch();
    }
 
    /**
     * 根据Id获取标签Id
     *
     * @param integer $id
     * @return array|null
     */
    public function getTagByRef(int $id):?array
    {
        return $this->tagTable->select(['id','tag'], ['ref'=>$id])->fetchAll();
    }
 
    public function getRefByTag(int $tagid, int $page=null, int $row=10)
    {
        return $this->tagTable->listWhere(['tag'=>$tagid], [], $page, $row);
    }

    public function search(string $tagname):PageData
    {
        return TablePager::search($this->tagsTable, 'name', $tagname);
    }

    /**
     * 绑定标签
     *
     * @param integer $ref 引用标签
     * @param array $tags 标签ID列表
     * @return integer
     */
    public function bindTags(int $ref, array $tags):int
    {
        $count=0;
        if ($get=$this->tagTable->select(['ref','tag'], ['ref'=>$ref])->fetchAll()) {
            foreach ($get as $item) {
                $id=array_search($item['tag'], $tags);
                if ($id!==false) {
                    unset($tags[$id]);
                }
            }
        }
        foreach ($tags as $tag) {
            $count++;
            $this->tagTable->insert(['ref'=>$ref,'tag'=>$tag]);
        }
        $this->tagsTable->update('count = count + 1', ['id'=>$tags]);
        return $count;
    }
    
    public function unbindAllTags(int $ref)
    {
        $tags = $this->getTagByRef($ref);
        if (is_array($tags)) {
            $ids = [];
            foreach ($tags as $item) {
                $ids [] = $item['tag'];
            }
            return $this->unbindTags($ref, $ids);
        }
    }

    public function unbindTags(int $ref, array $tags)
    {
        $this->tagsTable->update('count = count - 1', ['id'=>$tags]);
        return $this->tagTable->delete(['ref'=>$ref,'tag'=>$tags]);
    }
}
