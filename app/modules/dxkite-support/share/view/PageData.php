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

    protected function __construct()
    {
    }

    public function toArray():array
    {
        if (is_array($this->rows)) {
            $size = count($this->rows);
        } else {
            $size = 0;
            $rows = [];
        }
        if ($this->page) {
            $pervious =true;
            $maxPage = ceil($this->total / $this->pageSize);
            if ($this->page >= $maxPage) {
                $next =false;
            } else {
                $next = true;
            }
            if ($this->page <= 1) {
                $pervious=false;
            }
            return [
                'rows'=> $this->rows,
                'size'=>$size,
                'total' => $this->total,
                'page' =>[
                    'max' => $maxPage,
                    'min' => 1 ,
                    'size'=> $this->pageSize,
                    'current'=>$this->page,
                    'next' => $next,
                    'previous'=> $pervious,
                ]
            ];
        } else {
            return [
                'rows'=> $this->rows,
                'size'=> $size,
                'total' => $size,
                'page' =>[
                    'max' => 1,
                    'min' => 1 ,
                    'size'=> $size,
                    'current'=> 1,
                    'next' => false,
                    'previous'=> false,
                ]
            ];
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
}
