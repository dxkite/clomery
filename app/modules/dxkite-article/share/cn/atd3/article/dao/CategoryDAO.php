<?php
namespace cn\atd3\article\dao;

use suda\archive\DAO;
use suda\core\Query;

class CategoryDAO extends DAO
{
    public function __construct()
    {
        parent::__construct(conf('module.tables.category', 'article_category'));
    }

    public function onBuildCreator($table){
        return $table->fields(
            $table->field('id','bigint',20)->primary()->unsigned()->auto(),
            $table->field('name','varchar',255)->unique()->comment("分类名"),
            $table->field('slug','varchar',255)->unique()->comment("分类缩写"),
            $table->field('user','bigint',20)->unsigned()->key()->comment("创建用户"),
            $table->field('count','bigint',20)->key()->comment("文章统计"),
            $table->field('parent','bigint',20)->key()->comment("父分类")
        );
    }

    public function ids2name(array $ids)
    {
        $get=$this->select(['id','name'], ['id'=>$ids])->fetchAll();
        if ($get) {
            $result=[];
            foreach ($get as $item) {
                $result[$item['id']]=$item['name'];
            }
            return $result;
        }
        return false;
    }

    public function name2id(string $name)
    {
        if (is_null($page)) {
            return Query::where($this->getTableName(), $this->getFields(), ' LOWER(name)=LOWER(:name) ', ['name'=>$name])->fetch()['id']??false;
        }
        return Query::where($this->getTableName(), $this->getFields(), ' LOWER(name)=LOWER(:name) ', ['name'=>$name], [$page,$row])->fetch()['id']??false;
    }

    public function slug2id(string $slug)
    {
        if (is_null($page)) {
            return Query::where($this->getTableName(), $this->getFields(), ' LOWER(slug)=LOWER(:slug) ', ['slug'=>$name])->fetch()['id']??false;
        }
        return Query::where($this->getTableName(), $this->getFields(), ' LOWER(slug)=LOWER(:slug) ', ['slug'=>$name], [$page,$row])->fetch()['id']??false;
    }

    public function setCategory(int $uid, int $aid, int $cateid)
    {
        $article=new ArticleDAO;
        $get=$article->getUserArticle($uid, $aid);
        
        if ($get) {
            if ($get['category']==$cateid) {
                return true;
            }
            try {
                Query::begin();
                $article->setCategory($aid, $cateid);
                $this->countCate($cateid);
                $this->countCate($get['category'], false);
                Query::commit();
                return true;
            } catch (\PDOException $e) {
                Query::rollBack();
                return false;
            }
        }
        return false;
    }

    protected function countCate(int $cateid, bool $op=true)
    {
        if ($op) {
            return Query::update($this->getTableName(), 'count = count +1', ['id'=>$cateid]);
        } else {
            return Query::update($this->getTableName(), 'count = count -1', ['id'=>$cateid]);
        }
    }

    public function add(int $uid, string $name, string $slug, int $parent=0)
    {
        return $this->insert([
            'name'=>$name,
            'slug'=>$slug,
            'parent'=>$parent,
            'user'=>$uid,
        ]);
    }
}
