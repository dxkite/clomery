<?php


namespace clomery\main\provider;


use clomery\content\PageUtil;
use clomery\content\provider\ContentProvider;
use clomery\main\controller\ArticleController;
use clomery\main\table\ArticleTable;
use clomery\main\table\CategoryTable;
use clomery\main\table\TagRelationTable;
use clomery\main\table\TagTable;


class ArticleProvider extends ContentProvider
{
    /**
     * 导出的类
     */
    const EXPORTS = [ContentProvider::class, ArticleProvider::class];

    /**
     * @var ArticleController
     */
    protected $controller;

    public function __construct()
    {
        parent::__construct(new ArticleController(new ArticleTable(), new CategoryTable(), new TagTable(), new TagRelationTable()));
    }

    /**
     * @param int|null $begin
     * @param int|null $end
     * @param int|null $page
     * @param int $row
     * @return mixed
     * @throws \suda\database\exception\SQLException
     */
    public function getArchives(?int $begin = null, ?int $end = null, ?int $page = null, int $row = 10) {
        $data = $this->controller->getArchives($begin, $end, $page, $row);
        $data = PageUtil::parseKeyToColumn($data, 'date', [
            'raw' => function($date) {
                return $date;
            },
            'date' => function($date) {
                return \date_create_from_format('Y-m', $date)->format($this->application->_('Y年m月'));
            }
        ]);
        return $data;
    }

    /**
     * @param string $date
     * @param int|null $page
     * @param int $row
     * @return \support\openmethod\PageData
     * @throws \suda\database\exception\SQLException
     */
    public function getArticleListByDate(string $date, ?int $page=null, int $row=10) {
        $data = $this->controller->getListByDate($date, $page, $row);
        $data = PageUtil::parseKeyToColumn($data, 'id', [
            'tag' => function($relate) {
                return $this->controller->getTagController()->getTags($relate, ['id', 'name', 'slug']);
            }
        ]);
        $data = PageUtil::parseKeyToKey($data, 'category', [
            'category' => function ($categoryArray) {
                return $this->controller->getCategoryController()->getWithArray($categoryArray, ['id', 'name', 'slug']);
            }
        ]);
        return $data;
    }
}