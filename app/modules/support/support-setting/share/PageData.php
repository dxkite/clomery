<?php
namespace support\setting;

use JsonSerializable;
use suda\orm\struct\ReadStatement;
use suda\orm\struct\QueryStatement;

class PageData implements JsonSerializable
{
    /**
     * 列数据
     *
     * @var array|null
     */
    protected $rows;
    /**
     * 数据大小
     *
     * @var int
     */
    protected $size;
    /**
     * 数据总量
     *
     * @var int
     */
    protected $total;
    /**
     * 当前页
     *
     * @var int|null
     */
    protected $page;
    /**
     * 页大小
     *
     * @var int
     */
    protected $pageSize;
    /**
     * 最大页
     *
     * @var int
     */
    protected $pageMax;
    /**
     * 最小页
     *
     * @var int
     */
    protected $pageMin;
    /**
     * 下一页
     *
     * @var bool
     */
    protected $pageNext;
    /**
     * 上一页
     *
     * @var bool
     */
    protected $pagePrevious;
    /**
     * 当前页
     *
     * @var int
     */
    protected $pageCurrent;


    protected function __construct()
    {
    }

    public function toArray():array
    {
        return [
            'rows' => $this->rows,
            'size' => $this->size,
            'total' => $this->total,
            'page' => [
                'max' => $this->pageMax,
                'min' => $this->pageMin,
                'size' => $this->pageSize,
                'current' => $this->pageCurrent,
                'next' => $this->pageNext,
                'previous' => $this->pagePrevious,
            ]
        ];
    }

    protected function parsePageData()
    {
        if (is_array($this->rows)) {
            $this->size = count($this->rows);
        } else {
            $this->size = 0;
            $this->rows = [];
        }
        if ($this->page) {
            $previous = true;
            $maxPage = ceil($this->total / $this->pageSize);
            if ($this->page >= $maxPage) {
                $next = false;
            } else {
                $next = true;
            }
            if ($this->page <= 1) {
                $previous = false;
            }
            $this->pageMin = 1;
            $this->pageMax = $maxPage;
            $this->pageCurrent = $this->page;
            $this->pageNext = $next;
            $this->pagePrevious = $previous;
        } else {
            $this->pageMin = 1;
            $this->pageMax = 1;
            $this->pageCurrent = 1;
            $this->pageNext = false;
            $this->pagePrevious = false;
        }
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    protected static function build(?array $rows, int $total, ?int $page, int $pageSize):PageData
    {
        $pagedata = new PageData;
        $pagedata->rows = $rows;
        $pagedata->total = $total;
        $pagedata->page = $page;
        $pagedata->pageSize = $page === null ? $total: $pageSize;
        $pagedata->parsePageData();
        return $pagedata;
    }

    /**
     * 创建分页数据
     *
     * @param ReadStatement|QueryStatement $statement
     * @param integer $page
     * @param integer $row
     * @return PageData
     */
    public static function create($statement, int $page = null, int $row = 10): PageData
    {
        $access = $statement->getAccess();
        $fields = $access->getStruct()->getFields()->all();
        $total = clone $statement;
        if (count($fields) > 0 ){
            $field = \array_shift($fields);
            $total->read([$field->getName()]);
        }
        $totalQuery = new QueryStatement($access, 'SELECT count(*) as count from ('.$total.') as total', $statement->getBinder());
        if ($page !== null) {
            $statement->page($page, $row);
        }
        $data = $totalQuery->one();
        $total = intval($data['count']);
        return PageData::build($statement->all(), $total, $page, $row);
    }

    /**
     * 空页
     *
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     */
    public static function empty(?int $page = null, int $row = 10): PageData
    {
        return PageData::build([], 0, $page, $row);
    }

    /**
     * Get 列数据
     *
     * @return  array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Set 列数据
     *
     * @param  array  $rows  列数据
     *
     * @return  self
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;
        $this->parsePageData();
        return $this;
    }

    /**
     * Get 数据总量
     *
     * @return  int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set 数据总量
     *
     * @param  int  $total  数据总量
     *
     * @return  self
     */
    public function setTotal(int $total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get 获取分页属性
     *
     * @return  array
     */
    public function getPage()
    {
        return [
            'max' => $this->pageMax,
            'min' => $this->pageMin,
            'size' => $this->pageSize,
            'current' => $this->pageCurrent,
            'next' => $this->pageNext,
            'previous' => $this->pagePrevious,
        ];
    }

    /**
     * Get 页大小
     *
     * @return  int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Set 页大小
     *
     * @param  int  $pageSize  页大小
     *
     * @return  self
     */
    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * Get 数据大小
     *
     * @return  int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set 数据大小
     *
     * @param  int  $size  数据大小
     *
     * @return  self
     */
    public function setSize(int $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get 最大页
     *
     * @return  int
     */
    public function getPageMax()
    {
        return $this->pageMax;
    }

    /**
     * Set 最大页
     *
     * @param  int  $pageMax  最大页
     *
     * @return  self
     */
    public function setPageMax(int $pageMax)
    {
        $this->pageMax = $pageMax;

        return $this;
    }

    /**
     * Get 最小页
     *
     * @return  int
     */
    public function getPageMin()
    {
        return $this->pageMin;
    }

    /**
     * Set 最小页
     *
     * @param  int  $pageMin  最小页
     *
     * @return  self
     */
    public function setPageMin(int $pageMin)
    {
        $this->pageMin = $pageMin;

        return $this;
    }

    /**
     * Get 下一页
     *
     * @return  bool
     */
    public function getPageNext()
    {
        return $this->pageNext;
    }

    /**
     * Set 下一页
     *
     * @param  bool  $pageNext  下一页
     *
     * @return  self
     */
    public function setPageNext(bool $pageNext)
    {
        $this->pageNext = $pageNext;

        return $this;
    }

    /**
     * Get 上一页
     *
     * @return  bool
     */
    public function getPagePrevious()
    {
        return $this->pagePrevious;
    }

    /**
     * Set 上一页
     *
     * @param  bool  $pagePrevious  上一页
     *
     * @return  self
     */
    public function setPagePrevious(bool $pagePrevious)
    {
        $this->pagePrevious = $pagePrevious;

        return $this;
    }

    /**
     * Get 当前页
     *
     * @return  int
     */
    public function getPageCurrent()
    {
        return $this->pageCurrent;
    }

    /**
     * Set 当前页
     *
     * @param  int  $pageCurrent  当前页
     *
     * @return  self
     */
    public function setPageCurrent(int $pageCurrent)
    {
        $this->pageCurrent = $pageCurrent;

        return $this;
    }
}
