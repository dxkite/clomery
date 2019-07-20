<?php


namespace clomery\main\controller;


use clomery\content\controller\BaseController;
use clomery\content\controller\ContentController;
use clomery\main\table\ArticleTable;
use suda\application\database\Table;
use support\openmethod\PageData;

class ArticleController extends ContentController
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @param int|null $begin
     * @param int|null $end
     * @param int|null $page
     * @param int $row
     * @return PageData
     * @throws \suda\database\exception\SQLException
     */
    public function getArchives(?int $begin = null, ?int $end = null, ?int $page = null, int $row = 10): PageData
    {
        $where = [
            'status' => ArticleTable::PUBLISH,
        ];
        if (!is_null($begin)) {
            $where['create'] = ['=>', $begin];
        }
        if (!is_null($end)) {
            $where['create'] = ['<=', $end];
        }
        return PageData::create(
            $this->table
                ->read('FROM_UNIXTIME(`create_time`, \'%Y-%m\') AS date, count(id) AS count')
                ->where($where)
                ->groupBy('date')
                ->orderBy('date', 'DESC')
            , $page, $row);
    }

    /**
     * 根据日期获取列表
     *
     * @param integer|null $user
     * @param string $date
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     * @throws \suda\database\exception\SQLException
     */
    public function getListByDate(string $date, ?int $page=null, int $row=10):PageData
    {
        $parameter['date'] = $date;
        return PageData::create($this->table->read(static::$showFields)
            ->where('status = :publish AND FROM_UNIXTIME(`create_time`, \'%Y-%m\') = :date', [
                'publish' => ArticleTable::PUBLISH,
                'date' => $date
            ]), $page, $row);
    }
}