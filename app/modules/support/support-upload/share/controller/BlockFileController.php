<?php
/**
 * Created by IntelliJ IDEA.
 * User: dxkite
 * Date: 2019/4/28 0028
 * Time: 8:55
 */

namespace support\upload\controller;


use support\exception\BlockFileException;
use support\upload\BlockFile;
use support\upload\BlockFileWriter;

class BlockFileController
{
    /**
     * @var string
     */
    protected $remoteAddr;
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $tmpPath;

    public function __construct(string  $userId, string $remoteAddr, string $tmpPath)
    {
        $this->remoteAddr = $remoteAddr;
        $this->userId = $userId;
        $this->tmpPath = $tmpPath;
    }

    /**
     * 创建文件
     * @param string $name
     * @param string $md5
     * @return array|null
     * @throws BlockFileException
     */
    public function  create(string $name, string $md5) {
        if (strlen($this->userId) === 0 ) {
            throw  new BlockFileException("error user id", BlockFileException::ERR_USER );
        }
        return BlockFileWriter::create($name, $md5, $this->userId, $this->remoteAddr);
    }

    /**
     * 写入文件
     * @param BlockFile $file
     * @return array|null
     * @throws BlockFileException
     */
    public function write(BlockFile $file) {
        $data = BlockFileWriter::getSaveDataInfo($file->getId());
        if ($data === null) {
            throw new BlockFileException("error block id", BlockFileException::ERR_BLOCK_ID);
        }
        return BlockFileWriter::upload($this->tmpPath, $file);
    }

    /**
     * 完成上传
     * @param string $id
     * @param string $saveTo
     * @return array|null
     * @throws BlockFileException
     */
    public function finish(string $id, string $saveTo) {
        $data = BlockFileWriter::getSaveDataInfo($id);
        if ($data === null) {
            throw new BlockFileException("error block id", BlockFileException::ERR_BLOCK_ID);
        }
        if ($data['user'] !== $this->userId) {
            throw  new BlockFileException("error user id", BlockFileException::ERR_USER );
        }
        $finish = BlockFileWriter::finish($this->tmpPath, $id, $saveTo);
        if ($finish === true) {
            return [
                'id' => $data['id'],
                'hash' => $data['hash'],
            ];
        }
        return null;
    }

    /**
     * 取消上传
     * @param string $id
     * @return bool
     * @throws BlockFileException
     */
    public function cancel(string $id){
        $data = BlockFileWriter::getSaveDataInfo($id);
        if ($data === null) {
            throw new BlockFileException("error block id", BlockFileException::ERR_BLOCK_ID);
        }
        if ($data['user'] !== $this->userId) {
            throw  new BlockFileException("error user id", BlockFileException::ERR_USER );
        }
        return BlockFileWriter::cancel($this->tmpPath, $id);
    }
}