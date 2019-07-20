<?php
namespace support\upload\provider;

use support\exception\BlockFileException;
use support\openmethod\parameter\File;
use support\upload\BlockFile;
use support\upload\BlockFileWriter;
use support\upload\controller\BlockFileController;
use support\upload\UploadUtil;
use support\visitor\provider\UserSessionAwareProvider;

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
     * @return string
     * @throws \support\exception\BlockFileException
     */
    public function create(): string
    {
        $controller = new BlockFileController($this->application->getDataPath().'/upload/block-temp');
        return $controller->create();
    }

    /**
     * 分块写入文件
     * @param BlockFile $file
     * @return array|null
     * @throws \support\exception\BlockFileException
     */
    public function write(BlockFile $file):?array
    {
        $controller = new BlockFileController($this->application->getDataPath().'/upload/block-temp');
        return $controller->write($file);
    }

    /**
     * 完成文件上传
     * @param string $id
     * @param string $filename
     * @param string|null $md5
     * @return bool
     * @throws BlockFileException
     */
    public function finish(string $id, string $filename, ?string $md5 = null): bool
    {
        $extension = pathinfo($filename,PATHINFO_EXTENSION);
        $hash = $md5 ? $md5: md5($id.microtime(true));
        $saveName = UploadUtil::md5encode($hash);
        $path = $extension.'/'.$saveName.'.'.$extension;
        $savePath = $this->application->getDataPath().'/upload/'.$path;
        $controller = new BlockFileController($this->application->getDataPath().'/upload/block-temp');
        if ($controller->finish($id, $savePath)) {
            return '/upload/'.$path;
        }
        return null;
    }

    /**
     * 取消文件上传
     * @param string $id
     * @return bool
     * @throws BlockFileException
     */
    public function cancel(string $id)
    {
        $controller = new BlockFileController($this->application->getDataPath().'/upload/block-temp');
        return $controller->cancel($id);
    }

    /**
     * 上传文件直接上传
     *
     * @param File $file
     * @return string
     */
    public function upload(File $file) {
        $path = UploadUtil::save($this->application->getDataPath().'/upload', $file);
        return '/upload/'.$path;
    }
}
