<?php
namespace cn\atd3\article\dao;

use suda\archive\Table;
use suda\tool\Pinyin;
use suda\core\Request;
use suda\core\Query;

class ArticleDAO extends Table
{
    const STATUS_DELETE=0;     // 删除
    const STATUS_DRAFT=1;      // 草稿
    const STATUS_PUBLISH=2;    // 发布

    const TYPE_MARKDOWN=0;
    const TYPE_HTML=1;
    const TYPE_PLAIN=2;

    public function __construct()
    {
        parent::__construct('article');
    }

    public function onBuildCreator($table)
    {
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('user', 'bigint', 20)->unsigned()->key()->comment("作者"),
            $table->field('title', 'varchar', 255)->key()->comment("标题"),
            $table->field('slug', 'varchar', 255)->key()->comment("缩写"),
            $table->field('cover', 'bigint', 20)->comment("封面文件ID"),
            $table->field('abstract', 'text')->comment("摘要"),
            $table->field('content', 'text')->comment("内容"),
            $table->field('create', 'int', 11)->key()->comment("创建时间"),
            $table->field('modify', 'int', 11)->key()->comment("修改时间"),
            $table->field('ip', 'varchar', 32)->comment("编辑IP"),
            $table->field('views', 'int', 11)->key()->comment("阅读量"),
            $table->field('category', 'bigint', 20)->key()->comment("分类"),
            $table->field('type', 'tinyint', 1)->key()->comment("类型"),
            $table->field('status', 'tinyint', 1)->key()->comment("状态")
        );
    }

    public function create(int $user, string $title, string $content, int $category=0, int $type=ArticleDAO::TYPE_MARKDOWN, int $status=ArticleDAO::STATUS_DRAFT)
    {
        return $this->insert([
            'user'=>$user,
            'title'=>$title,
            'content'=>$content,
            'slug'=>Pinyin::getAll($title, '-', 255),
            'create'=>time(),
            'modify'=>time(),
            'views'=>0,
            'category'=>$category,
            'ip'=>Request::ip(),
            'type'=>$type,
            'status'=>$status,
        ]);
    }
    
    public function edit(int $id, array $update,int $user=null)
    {
        $update['modify']=time();
        if (isset($update['title']) && !isset($update['slug'])) {
            $update['slug']=$update['slug']??Pinyin::getAll($update['title'], '-', 255);
        }
        $update['ip']= Request::ip();
        if(is_null($user)){
            return $this->update($update, ['id'=>$id]);
        }
        return $this->update($update, ['id'=>$id, 'user'=>$user]);
    }

    public function getList(int $page=null, int $count=10)
    {
        $this->setWants(['id','title','slug','user','create','modify','category','abstract' ,'cover','views','status']);
        // OR ( user=:user  AND status!=:delete )
        if (is_null($page)) {
            return Query::where($this->getTableName(), $this->getWants(), ' status = :publish '.  self::_order(), ['publish'=>ArticleDAO::STATUS_PUBLISH])->fetchAll();
        } else {
            return Query::where($this->getTableName(), $this->getWants(), ' status = :publish '.  self::_order(), ['publish'=>ArticleDAO::STATUS_PUBLISH], [$page, $count])->fetchAll();
        }
    }

    public function getUserArticles(int $uid, int $page=null, int $count=10)
    {
        if (is_null($page)) {
            return $this->setWants(['id','title','slug','user','create','modify','category','abstract' ,'cover','views','status'])->listWhere(['user'=>$uid]);
        } else {
            return $this->setWants(['id','title','slug','user','create','modify','category','abstract' ,'cover','views','status'])->listWhere(['user'=>$uid], $page, $count);
        }
    }

    public function getListByCategory(int $cateid, int $page=null, int $count=10)
    {
        if (is_null($page)) {
            return $this->setWants(['id','title','slug','user','create','modify','category','abstract' ,'cover','views','status'])->listWhere(['category'=>$cateid]);
        } else {
            return $this->setWants(['id','title','slug','user','create','modify','category','abstract' ,'cover','views','status'])->listWhere(['category'=>$cateid], $page, $count);
        }
    }

    public function getArticle(int $article, int $user=null)
    {
        if (is_null($user)) {
            return Query::where($this->getTableName(), $this->getWants(), ' id=:id AND status = :publish ', ['id'=>$article,  'publish'=>ArticleDAO::STATUS_PUBLISH])->fetch();
        }
        return Query::where($this->getTableName(), $this->getWants(), ' ( id=:id AND status = :publish ) OR ( id=:id AND user=:user  AND status!=:delete)', ['id'=>$article, 'user'=>$user, 'publish'=>ArticleDAO::STATUS_PUBLISH,'delete'=>ArticleDAO::STATUS_DELETE])->fetch();
    }

    public function getUserArticle(int $uid, int $article)
    {
        return Query::where($this->getTableName(), $this->getWants(), [$this->getPrimaryKey()=>$article,'user'=>$uid])->fetch()?:false;
    }

    public function getArticleListByIds(array $ids)
    {
        list($in_sql, $in_params)=Query::prepareIn('id', $ids);
        $in_params['publish']=ArticleDAO::STATUS_PUBLISH;
        $this->setWants(['id','title','slug','user','create','modify','category','abstract' ,'cover','views','status']);
        return Query::where($this->getTableName(), $this->getWants(), $in_sql.' AND status = :publish  '. self::_order(), $in_params)->fetchAll();
    }

    public function getArticleContent(int $user, int $article)
    {
        return Query::where($this->getTableName(), ['id','user','content','status'], ' id=:id AND status = :publish OR ( user=:user  AND status!=:delete)', ['id'=>$article, 'user'=>$user, 'publish'=>ArticleDAO::STATUS_PUBLISH,'delete'=>ArticleDAO::STATUS_DELETE])->fetch();
    }

    public function setCategory(int $article, int $category)
    {
        return $this->updateByPrimaryKey($article, ['category'=>$category]);
    }

    public function deleteByUser(int $userid, int $article)
    {
        return Query::delete($this->getTableName(), ' id=:id and user=:user ', ['id'=>$article,'user'=>$userid]);
    }

    public function viewPlus(int $article)
    {
        return Query::update($this->getTableName(), 'views = views +1', ['id'=>$article]);
    }

    public function setStatus(int $article, int $status, int $uid=0)
    {
        if ($uid) {
            return $this->updateByPrimaryKey($article, ['status'=>$status,'user'=>$uid]);
        }
        return $this->updateByPrimaryKey($article, ['status'=>$status]);
    }

    public function setAbstract(int $article, string $abstract, int $uid=0)
    {
        if ($uid) {
            return $this->updateByPrimaryKey($article, ['abstract'=>$abstract,'user'=>$uid]);
        }
        return $this->updateByPrimaryKey($article, ['abstract'=>$abstract]);
    }

    public function setCover(int $article, int $cover, int $uid=0)
    {
        if ($uid) {
            return $this->updateByPrimaryKey($article, ['cover'=>$cover,'user'=>$uid]);
        }
        return $this->updateByPrimaryKey($article, ['cover'=>$cover]);
    }

    public function getCover(int $article)
    {
        return $this->setWants(['cover'])->getByPrimaryKey($article, ['id'=>$article,'status'=>ArticleDAO::STATUS_PUBLISH])['cover']?:false;
    }

    public function getCount()
    {
        return $this->count('status!=:status', ['status'=>ArticleDAO::STATUS_DELETE]);
    }
}
