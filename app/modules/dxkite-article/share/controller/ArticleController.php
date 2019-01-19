<?php
namespace dxkite\article\controller;

use dxkite\support\util\Pinyin;
use dxkite\support\view\PageData;
use dxkite\content\parser\Content;
use dxkite\support\view\TablePager;
use suda\archive\SQLStatementPrepare;
use dxkite\article\table\ArticleTable;
use dxkite\article\controller\ArticleController;

/**
 * 文章控制器
 *
 * 控制文章的内容处理
 */
class ArticleController
{
    /**
     * 文章数据表
     *
     * @var ArticleTable
     */
    protected $table;
    public static $showFields = ['id','title','slug','user','create','modify','category','excerpt' ,'cover','views','status'];
    public static $viewFields = ['id','title','slug','user','create','modify','category','excerpt','content' ,'cover','views','status'];
    
 
    protected $order = ArticleTable::ORDER_DESC;
    protected $orderBy = 'modify';

    public function __construct(string $prefix='')
    {
        $this->table = new ArticleTable($prefix);
        $this->table->order($this->orderBy, $this->order);
    }
    
    public function setOrder(string $field, int $type=ArticleTable::ORDER_DESC)
    {
        $this->order = $type;
        $this->orderBy = $field;
        $this->table->order($field, $type);
    }

    
    /**
     * 保存文章内容
     *
     * @param integer|null $id
     * @param integer $user
     * @param string $title
     * @param string|null $slug
     * @param integer $category
     * @param integer $cover
     * @param Content $excerpt
     * @param Content $content
     * @param integer|null $create
     * @param integer|null $modify
     * @param integer $status
     * @return integer
     */
    public function save(
        ?int $id =null,
        int $user,
        string $title,
        ?string $slug=null,
        int $category=0,
        int $cover= 0,

        Content $excerpt,
        Content $content,
        
        ?int $create=null,
        ?int $modify=null,
        int $status=ArticleTable::STATUS_DRAFT
    ) :int {
        if (is_null($id)) {
            return $this->table->insert([
                'user'=> $user,
                'title'=> $title,
                'slug'=> $slug ?? Pinyin::getAll($title, '-', 255),
                'category'=> $category,
                'cover'=> $cover,
    
                'excerpt' => $excerpt,
                'content'=> $content,
                
                'create'=> $create ?? time(),
                'modify'=> $modify ?? time(),
    
                'views'=> 0,
                
                'ip'=> request()->ip(),
                'status'=>$status,
            ]);
        } else {
            $this->table->updateByPrimaryKey($id, [
                'user'=>$user,
                'title'=>$title,
                'slug'=> $slug ?? Pinyin::getAll($title, '-', 255),
                'category'=>$category,
                'cover' => $cover,
                'excerpt' => $excerpt,
                'content'=>$content,
                'modify'=> $modify ?? time(),
                'ip'=> request()->ip(),
                'status'=>$status,
            ]);
            return $id;
        }
    }
    
    /**
     * 更新部分文章内容
     *
     * @param integer $id
     * @param array $update
     * @param integer $whereUser
     * @return int
     */
    public function update(int $id, array $update, int $whereUser=null):int
    {
        if (array_key_exists('title', $update)) {
            $update['slug']= $update['slug'] ?? Pinyin::getAll($update['title'], '-', 255);
        }
        if (array_key_exists('title', $update)) {
            $update['slug']= $update['slug'] ?? Pinyin::getAll($update['title'], '-', 255);
        }
        if (array_key_exists('create', $update)) {
            unset($update['create']);
        }
        if (array_key_exists('excerpt', $update)) {
            $update['excerpt'] =content_pack($update['excerpt'], Content::MD);
        }
        if (array_key_exists('content', $update)) {
            $update['content'] =content_pack($update['content'], Content::MD);
        }
        $update['ip']= request()->ip();
        if (is_null($whereUser)) {
            return $this->table->update($update, ['id'=>$id]);
        }
        return $this->table->update($update, ['id'=>$id, 'user'=>$whereUser]);
    }
 
   
    /**
     * 获取文章列表
     *
     * @param integer|null $user 当前登陆的用户
     * @param integer|null $categoryId 当前选择的分类
     * @param integer $page 当前页
     * @param integer $count 页大小
     * @return PageData
     */
    public function getList(?int $user=null, ?int $categoryId =null, int $page=null, int $count=10):PageData
    {
        list($condition, $parameter) = self::getUserViewCondition($user);
        if (!is_null($categoryId)) {
            $parameter['category']=$categoryId;
            $condition.= ' AND category = :category';
        }
        return  TablePager::listWhere($this->table->setWants(ArticleController::$showFields), $condition, $parameter, $page, $count);
    }

    /**
     * 获取文章内容
     *
     * @param integer|null $user 登陆用户ID
     * @param integer $article 当前文章ID
     * @return array|null
     */
    public function getArticle(?int $user=null, int $article):?array
    {
        $condition = 'id = :id';
        $parameter = [
            'id' => $article,
        ];
        list($cond, $par) = self::getUserViewCondition($user);
        $condition = $condition .' AND '. $cond;
        $parameter = array_merge($parameter, $par);
        return $this->table->select(ArticleController::$viewFields, $condition, $parameter)->fetch();
    }

    /**
     * 根据标题缩写获取文章
     *
     * @param integer|null $user
     * @param string $slug
     * @return array|null
     */
    public function getArticleBySlug(?int $user=null, string $slug):?array
    {
        $condition = 'LOWER(slug)=LOWER(:slug)';
        $parameter = [
            'slug' => $slug,
        ];
        list($cond, $par) = self::getUserViewCondition($user);
        $condition = $condition .' AND '. $cond;
        $parameter = array_merge($parameter, $par);
        return $this->table->select(ArticleController::$viewFields, $condition, $parameter)->fetch();
    }

    /**
     * 更新文章计数
     *
     * @param integer $article
     * @return integer
     */
    public function updateArticleViewCount(int $article):int
    {
        return $this->table->update('views = views + 1', ['id'=>$article]);
    }

    /**
     * 上一篇下一篇文章
     *
     * @param integer|null $user
     * @param integer $article
     * @return array
     */
    public function getNearArticle(?int $user=null, int $article):array
    {
        $create = $this->table->setWants(['create'])->getByPrimaryKey($article);
        return $this->getNearArticleByTime($user, $create['create']);
    }

    /**
     * 根据时间获取相近文章
     *
     * @param integer|null $user
     * @param integer $create
     * @return array
     */
    public function getNearArticleByTime(?int $user=null, int $create):array
    {
        list($condition, $parameter) =self::getUserViewCondition($user);
        $previousCondition = '`create` < :create  AND ' . $condition;
        $nextCondition = '`create` > :create AND ' . $condition;
        $parameter['create'] =  $create;
        $previous = $this->table->select(ArticleController::$showFields, $previousCondition .' order by `create` asc', $parameter, 1, 1)->fetch();
        $next = $this->table->select(ArticleController::$showFields, $nextCondition .' order by `create` asc', $parameter, 1, 1)->fetch();
        return [$previous,$next];
    }

    /**
     * 获取用户查看条件
     *
     * @param integer|null $user
     * @param string $prefix
     * @return array
     */
    public static function getUserViewCondition(?int $user=null,string $prefix=''):array
    {
        if (is_null($user)) {
            $condition = $prefix.'status = :publish';
            $parameter = [
                'publish'=>ArticleTable::STATUS_PUBLISH,
            ];
        } else {
            $condition = '(('.$prefix.'user = :user AND '.$prefix.'status != :delete) OR '.$prefix.'status = :publish)';
            $parameter = [
                'publish'=>ArticleTable::STATUS_PUBLISH,
                'user' => $user,
                'delete' => ArticleTable::STATUS_DELETE,
            ];
        }
        return [$condition,$parameter];
    }

    /**
     * 获取文章数目
     *
     * @param integer $user
     * @param integer|null $categoryId
     * @return integer
     */
    public function getArticleCount(int $user=null, ?int $categoryId =null):int
    {
        list($condition, $parameter) = self::getUserViewCondition($user);
        return $this->table->count($condition, $parameter);
    }

    /**
     * 根据Id获取列表
     *
     * @param integer|null $user
     * @param array $ids
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getArticleListByIds(?int $user=null, array $ids, ?int $page, int $count):PageData
    {
        list($condition, $parameter)= SQLStatementPrepare::prepareIn('id', $ids);
        list($conditionU, $parameterU) = self::getUserViewCondition($user);
        $condition = array_merge($condition, $conditionU);
        $parameter = array_merge($parameter, $parameterU);
        return  TablePager::listWhere($this->table->setWants(ArticleController::$showFields), $condition, $parameter, $page, $count);
    }

    /**
     * 搜索标题
     *
     * @param string $title 标题关键字
     * @param integer|null $user 指定用户
     * @param integer|null $category 指定分类
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function search(string $title, ?int $user=null, ?int $category=null, ?int $page, int $count=10):PageData
    {
        $condition = ' status = :publish';
        $parameter = [
            'publish'=>ArticleTable::STATUS_PUBLISH,
        ];
        if (!is_null($category)) {
            $condition = 'AND category = :category';
            $parameter ['category'] = $category;
        }
        return TablePager::search($this->table->setWants(ArticleController::$showFields), 'title', $title, $condition, $parameter, $page, $count);
    }

    /**
     * 删除文章
     *
     * @param integer $article 文章ID
     * @param integer|null $userId 指定用户的文章
     * @return integer
     */
    public function delete(int $article, ?int $userId =null):int
    {
        if ($userId) {
            return $this->table->update(['status'=>ArticleTable::STATUS_DELETE], ['user'=>$userId,'id'=>$article]);
        }
        return $this->table->updateByPrimaryKey($article, ['status'=>ArticleTable::STATUS_DELETE]);
    }
}
