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
        $where[] = 'status = :status';
        $binder = [
            'status' => ArticleTable::PUBLISH,
        ];
        if (!is_null($begin)) {
            $binder['create_start'] = $begin;
            $where[] = 'create >= :create_start';
        }
        if (!is_null($end)) {
            $binder['create_end'] = $end;
            $where[] = 'create <= :create_end';
        }
        return PageData::create(
            $this->table
                ->read('FROM_UNIXTIME(`create_time`, \'%Y-%m\') AS date, count(id) AS count')
                ->where(implode(' AND ', $where), $binder)
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
    public function getListByDate(string $date, ?int $page = null, int $row = 10): PageData
    {
        $parameter = [];
        $parameter['date'] = $date;
        return PageData::create($this->table->read(static::$showFields)
            ->where('status = :publish AND FROM_UNIXTIME(`create_time`, \'%Y-%m\') = :date', [
                'publish' => ArticleTable::PUBLISH,
                'date' => $date,
            ]), $page, $row);
    }
}