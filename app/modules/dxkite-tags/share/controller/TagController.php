<?php
namespace dxkite\tags\controller;

use dxkite\tags\table\TagsTable;
use dxkite\tags\table\TagTable;
use dxkite\support\view\TablePager;
use suda\tool\Command;

class TagController
{
    protected $tagsTable;
    protected $tagTable;
    protected $target;

    public function __construct(string $target = null)
    {
        $this->target = Command::newClassInstance($target);
        $this->tagsTable = new TagsTable($this->target);
        $this->tagTable = new TagTable($this->target);
    }

    public function add(string $name)
    {
        if ($tag = $this->getTagId($name)) {
            $user = get_user_id();
            return $tag;
        } else {
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
        return $this->tagsTable->list($page, $row);
    }

    public function getTagByIds(array $ids)
    {
        return $this->tagsTable->select(['id','name'], ['id'=>$ids])->fetchAll();
    }

    public function getTagId(string $name)
    {
        return $this->tagsTable->select(['id'], ' LOWER(name)=LOWER(:name) ', ['name'=>$name])->fetch()['id']??false;
    }
 
    public function getTagByRef(int $id)
    {
        return $this->tagTable->select(['id','tag'], ['ref'=>$id])->fetchAll();
    }
 
    public function getRefByTag(int $tagid, int $page=null, int $row=10)
    {
        return $this->tagTable->listWhere(['tag'=>$tagid], [], $page, $row);
    }

    public function search(string $tagname):array
    {
        return TablePager::search($this->tagsTable, 'name', $tagname);
    }

    public function bindTags(int $ref, array $tags)
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
        return $count;
    }
    
    public function unbindTags(int $ref, array $tags)
    {
        return $this->tagTable->delete(['ref'=>$ref,'tag'=>$tags]);
    }
}
