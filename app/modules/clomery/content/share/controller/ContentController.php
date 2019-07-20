<?php


namespace clomery\content\controller;

use ArrayObject;
use clomery\main\table\ArticleTable;
use suda\application\database\Table;
use suda\database\exception\SQLException;
use suda\database\statement\PrepareTrait;
use support\openmethod\PageData;

class ContentController extends CategoryController
{
    public static $showFields = ['id', 'slug', 'title', 'user', 'create_time', 'modify_time', 'category', 'description', 'image', 'views', 'status'];

    /**
     * @var TagController
     */
    protected $tagController;

    public function __construct(Table $table, Table $tag, Table $relate)
    {
        parent::__construct($table);
        $this->tagController = new TagController($tag, $relate);
    }

    /**
     * 获取 上一篇 下一篇 文章
     *
     * @param integer|null $user
     * @param integer $article
     * @return array
     * @throws SQLException
     */
    public function getNearArticle(int $article): array
    {
        $create = $this->table->read('create_time')->where(['id' => $article]);
        return $this->getNearArticleByTime($create['create']);
    }

    /**
     * 根据时间获取相近文章
     *
     * @param integer $create
     * @return array
     * @throws SQLException
     */
    public function getNearArticleByTime(int $create): array
    {
        $previousCondition = ['create_time' => ['<', $create]];
        $nextCondition = ['create_time' => ['>', $create]];
        $previous = $this->table->read(static::$showFields)->where($previousCondition)->orderBy('create_time', 'DESC')->one();
        $next = $this->table->read(static::$showFields)->where($nextCondition)->orderBy('create_time')->one();
        return [$previous, $next];
    }

    use PrepareTrait;

    /**
     * 筛选文章
     * @param null|string $search
     * @param null|string $category
     * @param array|null $tags
     * @param int|null $page
     * @param int $count
     * @param int $field
     * @param int $order
     * @return PageData
     * @throws SQLException
     */
    public function getArticleList(?string $search, ?string $category, ?array $tags, ?int $page = 1, int $count = 10, int $field = 0, int $order = 0): PageData
    {
        $wants = $this->prepareReadFields(static::$showFields, '_:article');
        $parameter = [];
        if (is_array($tags) && count($tags) > 0) {
            $query = $this->buildTagArrayFilter($wants, $tags, $parameter);
        } else {
            $query = $this->buildSimple($wants, $parameter);
        }
        $name = $this->table->getName();
        $condition = ' `_:'.$name.'`.`status` = :publish';
        $binder['publish'] = ArticleTable::PUBLISH;
        $condition = $this->buildCategoryFilter($category, $condition, $binder);
        $condition = $this->buildSearchFilter($search, $condition, $binder);
        $query = $query . ' WHERE ' . $condition;
        $query.= $this->buildOrder($field, $order);
        $parameter = array_merge($binder, $parameter);
        return PageData::create($this->table->query($query, $parameter), $page, $count);
    }

    /**
     * @param int $field
     * @param int $order
     * @return string
     */
    protected function buildOrder(int $field = 0, int $order = 0) {
        $name = $this->table->getName();
        $query = ' ORDER BY `_:'.$name.'`.`stick` DESC';
        $orderType = $order == 0 ? 'DESC' : 'ASC';
        if ($field == 0) {
            $query .= ', `_:'.$name.'`.`modify_time` '.$orderType;
        } else {
            $query .= ', `_:'.$name.'`.`create_time` '.$orderType;
        }
        return $query;
    }

    /**
     * @param string|null $search
     * @param string $condition
     * @param array $binder
     * @return string
     */
    protected function buildSearchFilter(?string $search, string $condition, array & $binder): string
    {
        if ($search !== null && mb_strlen($search) > 2) {
            $condition = 'title = LIKE :search AND ' . $condition;
            $binder['title'] = $this->buildSearch($search);
        }
        return $condition;
    }

    /**
     * @param string|null $category
     * @param string $condition
     * @param array $binder
     * @return string
     */
    protected function buildCategoryFilter(?string $category, string $condition, array & $binder): string
    {
        if ($category !== null) {
            $condition = '`category` = :category AND ' . $condition;
            $binder['category'] = $category;
        }
        return $condition;
    }

    /**
     * @param string $wants
     * @param array $binder
     * @return string
     */
    protected function buildSimple(string $wants, array & $binder): string
    {
        $articleName = $this->table->getName();
        $query = "SELECT {$wants} FROM _:{$articleName}";
        $binder = [];
        return $query;
    }


    /**
     * @param string $wants
     * @param array $tagId
     * @param array $binder
     * @return string
     */
    protected function buildTagArrayFilter(string $wants, array $tagId, array & $binder): string
    {
        $tag = $this->tagController->getTable();
        $tagTableName = $tag->getName();
        $tagRelate = $this->tagController->getRelationController()->getTable();
        $tagRelateTableName = $tagRelate->getName();
        $articleName = $this->table->getName();
        $query = "SELECT {$wants} FROM _:{$tagTableName} 
        JOIN _:{$tagRelateTableName} ON `_:$tagRelateTableName`.`item` IN (:tag)  
        JOIN _:{$articleName} ON `_:{$articleName}`.`id` = `_:$tagRelateTableName`.`item`";
        $binder['tag'] = new ArrayObject($tagId);
        return $query;
    }

    /**
     * @return TagController
     */
    public function getTagController(): TagController
    {
        return $this->tagController;
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
            return '%' . implode('%', $split) . '%';
        }
        return $search;
    }
}