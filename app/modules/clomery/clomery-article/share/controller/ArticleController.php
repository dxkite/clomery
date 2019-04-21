<?php
namespace clomery\article\controller;

use ArrayObject;
use suda\orm\TableStruct;
use clomery\article\Pinyin;
use clomery\article\DataUnit;
use support\setting\PageData;
use clomery\article\data\TagData;
use suda\orm\statement\PrepareTrait;
use clomery\article\data\ArticleData;
use clomery\article\data\CategoryData;
use clomery\article\data\TagRelateData;
use suda\application\database\DataAccess;
use suda\application\database\DataObject;

/**
 * 文章处理逻辑
 */
class ArticleController
{
    public static $showFields = ['id','title','slug','user','create','modify','category','excerpt' ,'image','views','status'];
    public static $viewFields = ['id','title','slug','user','create','modify','category','excerpt','content' ,'image','views','status'];

    /**
     * 逻辑单元
     *
     * @var DataUnit
     */
    protected $unit;

    /**
     * 控制器
     *
     * @var DataAccess
     */
    protected $access;

    public function __construct(DataUnit $unit = null)
    {
        $this->unit = $unit ?? $this->createUnit();
        $this->access = $this->unit->unit(ArticleData::class);
    }

    /**
     * 创建默认数据单元
     *
     * @return DataUnit
     */
    protected function createUnit():DataUnit
    {
        $unit = new DataUnit;
        $unit->push(ArticleData::class);
        $unit->push(CategoryData::class);
        $unit->push(TagData::class);
        $unit->push(TagRelateData::class);
        return $unit;
    }

    /**
     * 保存文章数据
     *
     * @param \suda\application\database\DataObject $data
     * @param array $tag
     * @param string|null $user
     * @return string
     */
    public function save(DataObject $data, array $tag, ?string $user = null, bool $createTag = false):string
    {
        $article = $this->saveArticle($data, $user);
        if (strlen($article) > 0) {
            $tagController = new TagController($this->unit);
            $tagController->saveTag($article, $tag, $createTag);
        }
        return $article;
    }

    /**
     * 保存文章
     *
     * @param \suda\application\database\DataObject $data
     * @param string|null $user
     * @return string
     */
    public function saveArticle(DataObject $data, ?string $user = null):string
    {
        if (isset($data['id'])) {
            unset($data['create']);
            $data['slug'] = $data['slug'] ?? Pinyin::getAll($data['title'], '-', 255);
            $data['modify'] = $data['modify'] ?? time();
            $where = ['id' => $data['id'] ];
            if ($user !== null) {
                $where['user'] = $user;
            }
            if ($this->access->write($data)->where($where)->ok()) {
                return $data['id'];
            }
        } else {
            $data['slug'] = $data['slug'] ?? Pinyin::getAll($data['title'], '-', 255);
            $data['create'] = $data['create'] ?? time();
            $data['modify'] = $data['modify'] ?? time();
            $data['views'] = 0;
            $data['status'] = $data['status'] ?? ArticleData::STATUS_PUBLISH;
            return $this->access->write($data)->id();
        }
        return '';
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
    public function getList(?int $user = null, ?int $categoryId = null, int $page = null, int $count = 10):PageData
    {
        list($condition, $parameter) = self::getUserViewCondition($user);
        if (null !== $categoryId) {
            $parameter['category'] = $categoryId;
            $condition = 'category = :category AND ' .  $condition;
        }
        return PageData::create($this->access->read(static::$showFields)->where($condition, $parameter), $page, $count);
    }

    /**
     * 获取置顶文章
     *
     * @param integer|null $user 当前登陆的用户
     * @param integer|null $categoryId 当前选择的分类
     * @param integer $page 当前页
     * @param integer $count 页大小
     * @return PageData
     */
    public function getStickList(?int $user = null, ?int $categoryId = null, int $page = null, int $count = 10):PageData
    {
        list($condition, $parameter) = self::getUserViewCondition($user);
        if (null !== $categoryId) {
            $parameter['category'] = $categoryId;
            $condition = ' stick =1 AND category = :category AND ' .  $condition;
        }
        return PageData::create($this->access->read(static::$showFields)->where($condition, $parameter), $page, $count);
    }

    /**
     * 获取文章内容
     *
     * @param integer|null $user
     * @param integer $article
     * @return \suda\application\database\DataObject|null
     */
    public function getArticle(?int $user = null, int $article):?DataObject
    {
        $condition = 'id = :id';
        $parameter = [
            'id' => $article,
        ];
        list($cond, $par) = self::getUserViewCondition($user);
        $condition = $condition .' AND '. $cond;
        $parameter = array_merge($parameter, $par);
        return $this->access->read(static::$viewFields)->where($condition, $parameter)->one();
    }

    /**
     * 根据标题缩写获取文章
     *
     * @param integer|null $user
     * @param string $slug
     * @return \suda\application\database\DataObject|null
     */
    public function getArticleBySlug(?int $user = null, string $slug):?DataObject
    {
        $condition = 'LOWER(slug)=LOWER(:slug)';
        $parameter = [
            'slug' => $slug,
        ];
        list($cond, $par) = self::getUserViewCondition($user);
        $condition = $condition .' AND '. $cond;
        $parameter = array_merge($parameter, $par);
        return $this->access->read(static::$viewFields)->where($condition, $parameter)->one();
    }

    /**
     * 更新文章计数
     *
     * @param integer $article
     * @return integer
     */
    public function updateArticleViewCount(int $article):int
    {
        return $this->access->write('views = views + 1')->where(['id' => $article])->rows();
    }

    /**
     * 获取 上一篇 下一篇 文章
     *
     * @param integer|null $user
     * @param integer $article
     * @return array
     */
    public function getNearArticle(?int $user = null, int $article):array
    {
        $create = $this->access->read('create')->where(['id' => $article]);
        return $this->getNearArticleByTime($user, $create['create']);
    }

    /**
     * 根据时间获取相近文章
     *
     * @param integer|null $user
     * @param integer $create
     * @return array
     */
    public function getNearArticleByTime(?int $user = null, int $create):array
    {
        list($condition, $parameter) = self::getUserViewCondition($user);
        $previousCondition = '`create` < :create  AND ' . $condition;
        $nextCondition = '`create` > :create AND ' . $condition;
        $parameter['create'] = $create;
        $previous = $this->access->read(static::$showFields)->where($previousCondition)->orderBy('create', 'DESC')->one();
        $next = $this->access->read(static::$showFields)->where($nextCondition)->orderBy('create')->one();
        return [$previous,$next];
    }

    /**
     * 获取用户查看条件
     *
     * @param integer|null $user
     * @return array
     */
    public static function getUserViewCondition(?int $user = null):array
    {
        if (null === $user) {
            $condition = 'status = :publish';
            $parameter = [
                'publish' => ArticleData::STATUS_PUBLISH,
            ];
        } else {
            $condition = '((user = :user AND status != :delete) OR status = :publish)';
            $parameter = [
                'publish' => ArticleData::STATUS_PUBLISH,
                'user' => $user,
                'delete' => ArticleData::STATUS_DELETE,
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
    public function getArticleCount(int $user = null, ?int $categoryId = null):int
    {
        list($condition, $parameter) = self::getUserViewCondition($user);
        if ($categoryId !== null) {
            $condition = 'category = :category AND '. $condition;
            $parameter['category'] = $categoryId;
        }
        return $this->access->count($condition, $parameter);
    }

    use PrepareTrait;

    /**
     * 筛选文章
     *
     * @param integer|null $user
     * @param string $search
     * @param string $category
     * @param array $tagId
     * @param integer|null $page
     * @param integer $count
     * @return \support\setting\PageData
     */
    public function getArticleList(?int $user = null, string $search = null, string $category = null, array $tagId = null, ?int $page, int $count):PageData
    {
        $wants = $this->prepareReadFields(static::$showFields, 'article');
        if (\is_array($tagId)) {
            $query = $this->buildTagArrayFilter($wants, $tagId, $parameter);
        } else {
            $query = $this->buildSimple($wants, $parameter);
        }
        list($condition, $binder) = self::getUserViewCondition($user);
        $condition = $this->buildCategoryFilter($search, $category, $binder);
        $condition = $this->buildSearchFilter($search, $condition, $binder);
        $query = $query.' WHERE '. $condition;
        $parameter = array_merge($binder, $parameter);
        return PageData::create($this->access->query($query, $parameter), $page, $count);
    }

    
    protected function buildSearchFilter(?string $search = null, string $condition, array & $binder):string
    {
        if ($search !== null && \mb_strlen($search) > 2) {
            $condition = 'title = LIKE :search AND '. $condition;
            $binder['title'] = $this->buildSearch($search);
        }
        return $condition;
    }

    protected function buildCategoryFilter(?string $category = null, string $condition, array & $binder):string
    {
        if ($category !== null) {
            $condition = 'category = :category AND '. $condition;
            $binder['category'] = $category;
        }
        return $condition;
    }
    
    protected function buildSimple(string $wants, array & $binder):string
    {
        $articleName = $this->access->getName();
        $query = "SELECT {$wants} FROM _:{$articleName}";
        $binder = [];
        return $query;
    }

    protected function buildTagArrayFilter(string $wants, array $tagId, array & $binder):string
    {
        $tag = $this->unit->unit(TagData::class);
        $tagTableName = $tag->getName();
        $tagRelate = $this->unit->unit(TagRelateData::class);
        $tagRelateTableName = $tagRelate->getName();
        $articleName = $this->access->getName();
        $query = "SELECT {$wants} FROM _:{$tagTableName} 
        JOIN _:{$tagRelateTableName} ON `_:$tagRelateTableName`.`tag` IN (:tag)  
        JOIN _:{$articleName} ON `_:{$articleName}`.`id` = `_:$tagRelateTableName`.`tag`";
        $binder['tag'] = new ArrayObject($tagId);
        return $query;
    }

    /**
     * 构建搜索语句
     *
     * @param string $search
     * @return string
     */
    protected function buildSearch(string $search): string
    {
        if (strlen($search) > 80) {
            $search = substr($search, 0, 80);
        }
        $search = str_replace('%', '', $search);
        $split = preg_split('/\s+/', $search);
        if (is_array($split)) {
            array_filter($split);
            return '%'.implode('%', $split) .'%';
        }
        return $search;
    }

    /**
     * 删除文章
     *
     * @param string $article 文章ID
     * @param string|null $userId 指定用户的文章
     * @return integer
     */
    public function delete(string $article, ?string $userId = null):int
    {
        if ($userId !== null) {
            return $this->access->write(['status' => ArticleData::STATUS_DELETE])->where(['id' => $article, 'user' => $userId])->rows();
        }
        return $this->access->write(['status' => ArticleData::STATUS_DELETE])->where(['id' => $article])->rows();
    }
}
