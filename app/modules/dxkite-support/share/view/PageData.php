<?php
namespace dxkite\support\view;

use JsonSerializable;

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
    protected $pagePervious;
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
            'rows'=> $this->rows,
            'size'=>$this->size,
            'total' => $this->total,
            'page' =>[
                'max' => $this->pageMax,
                'min' => $this->pageMin,
                'size'=> $this->pageSize,
                'current'=>$this->pageCurrent,
                'next' => $this->pageNext,
                'previous'=> $this->pagePervious,
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
            $pervious =true;
            $maxPage = ceil($this->total / $this->pageSize);
            if ($this->page >= $maxPage) {
                $next = false;
            } else {
                $next = true;
            }
            if ($this->page <= 1) {
                $pervious=false;
            }
            $this->pageMin = 1;
            $this->pageMax = $maxPage;
            $this->pageCurrent = $this->page;
            $this->pageNext = $next;
            $this->pagePervious = $pervious;
        } else {
            $this->pageMin = 1;
            $this->pageMax = 1;
            $this->pageCurrent = 1;
            $this->pageNext = false;
            $this->pagePervious = false;
        }
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function build(?array $rows, int $total, ?int $page, int $pageSize):PageData
    {
        $pagedata = new PageData;
        $pagedata->rows = $rows;
        $pagedata->total = $total;
        $pagedata->page = $page;
        $pagedata->pageSize = $pageSize;
        $pagedata->parsePageData();
        return $pagedata;
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
     * Get 当前页
     *
     * @return  int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set 当前页
     *
     * @param  int  $page  当前页
     *
     * @return  self
     */
    public function setPage(int $page)
    {
        $this->page = $page;

        return $this;
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
    public function getPagePervious()
    {
        return $this->pagePervious;
    }

    /**
     * Set 上一页
     *
     * @param  bool  $pagePervious  上一页
     *
     * @return  self
     */
    public function setPagePervious(bool $pagePervious)
    {
        $this->pagePervious = $pagePervious;

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
