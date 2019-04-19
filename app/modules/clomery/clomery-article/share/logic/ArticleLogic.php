<?php
namespace clomery\article\logic;

use ArrayObject;
use suda\orm\TableStruct;
use clomery\article\Pinyin;
use support\setting\PageData;
use suda\orm\statement\PrepareTrait;
use clomery\article\data\ArticleData;
use suda\application\database\DataAccess;

/**
 * 文章处理逻辑
 */
class ArticleLogic
{
    public static $showFields = ['id','title','slug','user','create','modify','category','excerpt' ,'image','views','status'];
    public static $viewFields = ['id','title','slug','user','create','modify','category','excerpt','content' ,'image','views','status'];
    

    /**
     * 控制器
     *
     * @var DataAccess
     */
    protected $access;

    public function __construct(string $name = ArticleData::class)
    {
        $this->access = DataAccess::new($name);
    }

    /**
     * 文章数据
     *
     * @param ArticleData $data
     * @return integer
     */
    public function save(ArticleData $data, int $user = null):int
    {
        if (isset($data->id)) {
            unset($data->create);
            $data->slug = $data->slug ?? Pinyin::getAll($data->title, '-', 255);
            $data->modify = $data->modify ?? time();
            $where = ['id' => $data->id ];
            if ($user !== null) {
                $where['user'] = $user;
            }
            if ($this->access->write($data)->where($where)->ok()) {
                return $data->id;
            }
        } else {
            $data->slug = $data->slug ?? Pinyin::getAll($data->title, '-', 255);
            $data->create = $data->create ?? time();
            $data->modify = $data->modify ?? time();
            $data->views = 0;
            $data->status = $data->status ?? ArticleData::STATUS_PUBLISH;
            return $this->access->write($data)->id();
        }
        return 0;
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
     * @return \clomery\article\data\ArticleData|null
     */
    public function getArticle(?int $user = null, int $article):?ArticleData
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
     * @return \clomery\article\data\ArticleData|null
     */
    public function getArticleBySlug(?int $user = null, string $slug):?ArticleData
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
        $previous = $this->access->read(ArticleData::$showFields)->where($previousCondition)->orderBy('create', 'DESC')->one();
        $next = $this->access->read(ArticleData::$showFields)->where($nextCondition)->orderBy('create')->one();
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
     * 根据Id获取列表
     *
     * @param integer|null $user
     * @param array $ids
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getArticleListByTag(?int $user = null, int $tagId, ?int $page, int $count):PageData
    {
        $wants = $this->prepareReadFields(ArticleData::$showFields, 'article');
        $query = "SELECT {$wants} FROM _:tag 
        JOIN _:tag_relation ON `_:tag_relation`.`tag` = :tag  
        JOIN _:article ON `_:article`.`id` = `_:tag_relation`.`tag`";
        list($condition, $binder) = self::getUserViewCondition($user);
        $query = $query.' WHERE '. $condition;
        $binder['tag'] = $tagId;
        return PageData::create($this->access->query($query, $parameter), $page, $count);
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
    public function search(string $title, ?int $user = null, ?int $category = null, ?int $page, int $count = 10):PageData
    {
        $where = [
            'status' => ArticleTable::STATUS_PUBLISH,
            'title' => ['like' , $this->buildSearch($title) ],
        ];
        if (null !== $category) {
            $where ['category'] = $category;
        }
        return PageData::create($this->access->read(ArticleData::$showFields)->where($where), $page, $count);
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
        $search = preg_split('/\s+/', $search);
        array_filter($search);
        return '%'.implode('%', $search) .'%';
    }

    /**
     * 删除文章
     *
     * @param integer $article 文章ID
     * @param integer|null $userId 指定用户的文章
     * @return integer
     */
    public function delete(int $article, ?int $userId = null):int
    {
        if ($userId) {
            return $this->access->write(['status' => ArticleTable::STATUS_DELETE])->where(['id' => $article, 'user' => $userId])->rows();
        }
        return $this->access->write(['status' => ArticleTable::STATUS_DELETE])->where(['id' => $article])->rows();
    }
}
