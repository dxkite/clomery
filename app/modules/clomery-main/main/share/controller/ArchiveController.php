<?php

namespace dxkite\clomery\main\controller;

use dxkite\support\view\PageData;
use dxkite\support\view\TablePager;
use dxkite\article\controller\ArticleController;

class ArchiveController extends ArticleController
{
    public function getArchive(?int $user=null, ?int $begin = null, ?int $end = null, ?int $page=null, int $count=10):PageData
    {
        list($condition, $parameter)= ArticleController::getUserViewCondition($user);
        if (!is_null($begin)) {
            $condition.' AND `modify` => :begin ';
            $parameter['begin'] = $begin;
        }
        if (!is_null($end)) {
            $condition.' AND `modify` <= :end ';
            $parameter['end'] = $end;
        }
        return TablePager::select(
            $this->table,
            'FROM_UNIXTIME(`modify`, \'%Y-%m-%d\') AS date, count(id) AS count',
            $condition.' GROUP BY date',
            $parameter,
            $page,
            $count
        );
    }

    /**
     * 根据日期获取列表
     *
     * @param integer|null $user
     * @param string $date
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     */
    public function getListByDate(?int $user=null,string $date, ?int $page=null, int $count=10):PageData
    {
        list($condition, $parameter)= ArticleController::getUserViewCondition($user);
        $condition = 'FROM_UNIXTIME(`modify`, \'%Y-%m-%d\') = :date AND '.$condition;
        $parameter['date'] = $date;
        return TablePager::listWhere($this->table, $condition,$parameter,$page,$count);
    }
}
