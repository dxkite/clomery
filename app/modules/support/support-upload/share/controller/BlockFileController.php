<?php

namespace support\upload\controller;


use support\exception\BlockFileException;
use support\upload\BlockFile;
use support\upload\BlockFileWriter;

class BlockFileController
{

    /**
     * @var string
     */
    protected $tmpPath;

    public function __construct(string $tmpPath)
    {
        $this->tmpPath = $tmpPath;
    }

    /**
     * 创建文件
     * @param string $name
     * @param string $md5
     * @return string
     */
    public function create() {
        return BlockFileWriter::create();
    }

    /**
     * 写入文件
     * @param BlockFile $file
     * @return array|null
     * @throws BlockFileException
     */
    public function write(BlockFile $file) {
        if (BlockFileWriter::exist($this->tmpPath, $file->getId())) {
            throw new BlockFileException("error block id", BlockFileException::ERR_BLOCK_ID);
        }
        return BlockFileWriter::upload($this->tmpPath, $file);
    }

    /**
     * 完成上传
     * @param string $id
     * @param string $saveTo
     * @return bool
     * @throws BlockFileException
     */
    public function finish(string $id, string $saveTo) {
        if (BlockFileWriter::exist($this->tmpPath, $id)) {
            throw new BlockFileException("error block id", BlockFileException::ERR_BLOCK_ID);
        }
        return BlockFileWriter::finish($this->tmpPath, $id, $saveTo);
    }

    /**
     * 取消上传
     * @param string $id
     * @return bool
     * @throws BlockFileException
     */
    public function cancel(string $id){
        if (BlockFileWriter::exist($this->tmpPath, $id)) {
            throw new BlockFileException("error block id", BlockFileException::ERR_BLOCK_ID);
        }
        return BlockFileWriter::cancel($this->tmpPath, $id);
    }
}