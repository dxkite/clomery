<?php
namespace support\upload;

use suda\framework\http\UploadedFile;
use support\openmethod\MethodParameterBag;
use support\openmethod\MethodParameterInterface;

/**
 * 文件块
 */
class BlockFile implements MethodParameterInterface
{
    /**
     * 文件块ID
     *
     * @var string
     */
    protected $id;

    /**
     * 开始位置
     *
     * @var string
     */
    protected $rangeStart;

    /**
     * 结束位置
     *
     * @var string|null
     */
    protected $rangeStop = null;

    /**
     * 文件大小
     *
     * @var string
     */
    protected $size;

    /**
     * 输入数据
     *
     * @var string|UploadedFile
     */
    protected $data;

    /**
     * 创建参数
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \support\openmethod\MethodParameterBag $bag
     * @return mixed
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag)
    {
        $blockFile = new BlockFile;
        $request = $bag->getRequest();
        $rangeInfo = '';
        if ($request->hasHeader('content-range')) {
            $rangeInfo = trim($request->getHeader('content-range'));
        } elseif ($request->hasPost('content-range')) {
            $rangeInfo = trim($request->post('content-range'));
        }
        $range = static::getRangeInfo($rangeInfo);
        if ($range === null) {
            return null;
        }
        $file = $request->file('file');
        if ($file === null) {
            $blockFile->data = $request->input();
        } else {
            $blockFile->data = $file;
        }
        list(
            $blockFile->rangeStart,
            $blockFile->rangeStop,
            $blockFile->size
        ) = $range;
        return $blockFile;
    }

    /**
     * 获取range信息
     *
     * @param string $range
     * @return null
     */
    public static function getRangeInfo(string $range)
    {
        if (\preg_match('/^bytes\s+(\d+)\-(\d+)?\/(\d+)$/', $range, $match)) {
            $rangeStart = $match[1];
            $fileSize = $match[3];
            $rangeStop = $match[2];
            if (strlen($rangeStop) == 0 || $rangeStop - $rangeStart <= 0) {
                $rangeStop = $fileSize;
            }
            return [$rangeStart, $rangeStop, $fileSize];
        }
        return null;
    }

    /**
     * Get 输入数据
     *
     * @return  string|UploadedFile
     */ 
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get 文件大小
     *
     * @return  string
     */ 
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get 结束位置
     *
     * @return  string|null
     */ 
    public function getRangeStop()
    {
        return $this->rangeStop;
    }

    /**
     * Get 开始位置
     *
     * @return  string
     */ 
    public function getRangeStart()
    {
        return $this->rangeStart;
    }

    /**
     * Get 文件块ID
     *
     * @return  string
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set 文件块ID
     *
     * @param  string  $id  文件块ID
     *
     * @return  self
     */ 
    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set 开始位置
     *
     * @param  string  $rangeStart  开始位置
     *
     * @return  self
     */ 
    public function setRangeStart(string $rangeStart)
    {
        $this->rangeStart = $rangeStart;

        return $this;
    }

    /**
     * Set 结束位置
     *
     * @param  string|null  $rangeStop  结束位置
     *
     * @return  self
     */ 
    public function setRangeStop($rangeStop)
    {
        $this->rangeStop = $rangeStop;

        return $this;
    }

    /**
     * Set 文件大小
     *
     * @param  string  $size  文件大小
     *
     * @return  self
     */ 
    public function setSize(string $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Set 输入数据
     *
     * @param  string|UploadedFile  $data  输入数据
     *
     * @return  self
     */ 
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
