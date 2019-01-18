<?php
namespace dxkite\clomery\main\provider;

use dxkite\support\view\PageData;
use dxkite\article\provider\ArticleProvider;
use dxkite\clomery\main\controller\ArchiveController;

class ArchiveProvider extends ArticleProvider
{
    /**
     * 归档辅助查询
     *
     * @var ArchiveController
     */
    protected $archive;

    public function __construct()
    {
        parent::__construct();
        $this->archive = new ArchiveController;
    }

    /**
     * 获取归档信息
     *
     * @param string|null $begin 开始日期
     * @param string|null $end 结束日期
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getArchive(?string $begin=null, ?string $end=null, ?int $page=null, int $count=10):PageData
    {
        $userid = null;
        if (!\visitor()->isGuest()) {
            $userid = \get_user_id();
        }
        $beginUnix = null;
        if (!is_null($begin)) {
            $beginUnix = \date_create_from_format('Y-m-d', $begin)->getTimestamp();
        }
        $endUnix = null;
        if (!is_null($end)) {
            $endUnix = \date_create_from_format('Y-m-d', $end)->getTimestamp();
        }
        return $this->fixDatetime($this->archive->getArchive($userid, $beginUnix, $endUnix, $page, $count));
    }

    /**
     * 根据日期获取列表
     *
     * @param string $date
     * @return PageData
     */
    public function getListByDate(string $date, ?int $page=null, int $count=10):PageData
    {
        $userid = null;
        if (!\visitor()->isGuest()) {
            $userid = \get_user_id();
        }
        $data = $this->archive->getListByDate($userid, $date, $page, $count);
        return $this->view->listView($data);
    }

    public function fixDatetime(PageData $data):PageData
    {
        $datas = $data->getRows();
        foreach ($datas as $index => $item) {
            $date = \date_create_from_format('Y-m-d', $item['date'])->format(__('Y年m月d日'));
            $datas[$index] = [
                'date' => $date,
                'raw' => $item['date'],
                'count' => $item['count'],
            ];
        }
        return $data->setRows($datas);
    }
}
