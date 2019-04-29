<?php
namespace support\upload\provider;

use support\exception\BlockFileException;
use support\openmethod\parameter\File;
use support\upload\BlockFile;
use support\upload\BlockFileWriter;
use support\setting\provider\UserSessionAwareProvider;
use support\upload\controller\BlockFileController;
use support\upload\UploadUtil;

/**
 * 文件分片上传/单个小文件上传接口控制
 *
 * Class BlockUploadProvider
 * @package support\upload\provider
 */
class BlockUploadProvider extends UserSessionAwareProvider
{
    /**
     * 创建上传文件
     * @param string $name
     * @param string $md5
     * @return array|null
     * @throws \support\exception\BlockFileException
     */
    public function create(string $name, string $md5):?array
    {
        $controller = new BlockFileController($this->visitor->getId(), $this->request->getRemoteAddr(),$this->application->getDataPath().'/upload-temp');
        return $controller->create($name, $md5);
    }

    /**
     * 分块写入文件
     * @param BlockFile $file
     * @return array|null
     * @throws \support\exception\BlockFileException
     */
    public function write(BlockFile $file):?array
    {
        $controller = new BlockFileController($this->visitor->getId(), $this->request->getRemoteAddr(),$this->application->getDataPath().'/upload-temp');
        return $controller->write($file);
    }

    /**
     * 完成文件上传
     * @param string $id
     * @return array|null
     * @throws BlockFileException
     */
    public function finish(string $id):?array
    {
        $data = BlockFileWriter::getSaveDataInfo($id);
        if ($data === null) {
            throw new BlockFileException("error block id", BlockFileException::ERR_BLOCK_ID);
        }
        $savePath = $this->application->getDataPath().'/upload/'.$data['type'].'/'.$data['hash'].'.'.$data['type'];
        $controller = new BlockFileController($this->visitor->getId(), $this->request->getRemoteAddr(),$this->application->getDataPath().'/upload-temp');
        return $controller->finish($id, $savePath);
    }

    /**
     * 取消文件上传
     * @param string $id
     * @return bool
     * @throws BlockFileException
     */
    public function cancel(string $id)
    {
        $controller = new BlockFileController($this->visitor->getId(), $this->request->getRemoteAddr(),$this->application->getDataPath().'/upload-temp');
        return $controller->cancel($id);
    }

    /**
     * 上传文件直接上传
     *
     * @param File $file
     * @return array|null
     */
    public function upload(File $file) {
        return UploadUtil::saveFileDatabase($file, $this->visitor->getId(), $this->request->getRemoteAddr());
    }
}
