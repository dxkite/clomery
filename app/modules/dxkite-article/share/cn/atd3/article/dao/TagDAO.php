<?php
namespace cn\atd3\article\dao;

use suda\archive\Table;

class TagDAO extends Table
{
    public function __construct()
    {
        parent::__construct('article_tag');
    }

    public function onBuildCreator($table){
        return $table->fields(
            $table->field('id','bigint',20)->primary()->unsigned()->auto(),
            $table->field('ref','bigint',20)->unsigned()->key()->foreign((new ArticleDAO)->getCreator()->getField('id')),
            $table->field('tag','bigint',20)->unsigned()->key()->foreign((new TagsDAO)->getCreator()->getField('id'))
        );
    }

    /**
     * 根据文章获取标签
     *
     * @param int $id
     * @return void
     */
    public function get(int $id)
    {
        return $this->select(['id','ref','tag'], ['ref'=>$id])->fetchAll();
    }

    /**
     * 根据标签获取文章
     *
     * @param int $tagid
     * @param int $page
     * @param int $row
     * @return void
     */
    public function getRefByTag(int $tagid, int $page=null, int $row=10)
    {
        if (is_null($page)) {
            return $this->listWhere(['tag'=>$tagid]);
        }
        return $this->listWhere(['tag'=>$tagid],[], $page, $row);
    }

    public function bind(int $ref, array $tags)
    {
        $count=0;
        // 剔除已经存在的
        if ($get=$this->select(['ref','tag'], ['ref'=>$ref])->fetchAll()) {
            foreach ($get as $item) {
                $id=array_search($item['tag'], $tags);
                if ($id!==false) {
                    unset($tags[$id]);
                }
            }
        }
        // 添加新的
        foreach ($tags as $tag) {
            $count++;
            $this->insert(['ref'=>$ref,'tag'=>$tag]);
        }
        return $count;
    }
    
    public function unbind(int $ref, array $tags)
    {
        return $this->delete(['ref'=>$ref,'tag'=>$tags]);
    }
}
