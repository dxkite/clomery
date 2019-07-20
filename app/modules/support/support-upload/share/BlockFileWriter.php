<?php
namespace support\upload;

use suda\framework\http\UploadedFile;
use suda\framework\filesystem\FileSystem;

/**
 * 块文件写入工具
 */
class BlockFileWriter
{
    /**
     * 创建上传文件ID
     * @param string $name
     * @return string
     */
    public static function create() {
        $md5 = md5(uniqid().microtime(true));
        return UploadUtil::md5encode($md5);
    }

    /**
     * 上传分片
     *
     * @param string $tmpPath
     * @param BlockFile $file
     * @return array|null
     */
    public static function upload(string $tmpPath, BlockFile $file)
    {
        $data = $file->getData();
        $id = $file->getId();
        $md5 = static::getDataHash($data);
        $hash = UploadUtil::md5encode($md5);
        $filePartName = $hash.'.part';
        $index = 'part.index';
        $savePath = $tmpPath.'/'.$id;
        FileSystem::make($savePath);
        $indexOk = static::saveDataIndex($savePath.'/'.$index, $file, $hash);
        $dataOk = static::saveData($savePath.'/'.$filePartName, $file);
        if ($indexOk && $dataOk) {
            return [
                'md5' => $md5,
                'range' => [
                    'start' => $file->getRangeStart(),
                    'stop' => $file->getRangeStop(),
                    'size' => $file->getSize(),
                ],
            ];
        }
        return null;
    }

    /**
     * 判断文件ID是否存在
     * @param string $tmpPath
     * @param string $id
     * @return bool
     */
    public static function exist(string $tmpPath, string $id) {
        $indexPath = $tmpPath.'/'.$id.'/part.index';
        if (FileSystem::exist($indexPath)) {
            return true;
        }
        return false;
    }

    /**
     * 获取数据HASH
     *
     * @param UploadedFile|string $data
     * @return string
     */
    protected static function getDataHash($data)
    {
        if ($data instanceof UploadedFile) {
            return md5_file($data->getTempname());
        } else {
            return md5($data);
        }
    }

    /**
     * 保存索引数据
     *
     * @param string $index
     * @param BlockFile $file
     * @param string $hash
     * @return boolean
     */
    protected static function saveDataIndex(string $index, BlockFile $file, string $hash):bool
    {
        $indexFile = fopen($index, 'ab+');
        if ($indexFile !== false) {
            fputcsv($indexFile, [$file->getRangeStart(), $file->getRangeStop(), $hash]);
            fclose($indexFile);
            return true;
        }
        return false;
    }

    /**
     * 保存文件块数据
     *
     * @param string $path
     * @param BlockFile $file
     * @return bool
     */
    protected static function saveData(string $path, BlockFile $file)
    {
        $data = $file->getData();
        if ($data instanceof UploadedFile) {
            return FileSystem::move($data->getTempname(), $path);
        } else {
            return FileSystem::put($path, $data);
        }
    }

    /**
     * 保存文件
     *
     * @param string $tmpPath
     * @param string $id
     * @param string $savePath
     * @return bool
     */
    public static function finish(string $tmpPath, string $id, string $savePath)
    {
        $indexPath = $tmpPath.'/'.$id.'/part.index';
        if (FileSystem::exist($indexPath)) {
            FileSystem::make(dirname($savePath));
            $fileWriter = new FileWriter($savePath);
            if (($handle = fopen($indexPath, 'rb')) !== false) {
                while (($data = fgetcsv($handle, 64)) !== false) {
                    list($start, $stop, $partHash) = $data;
                    $blockPath = $tmpPath.'/'.$id.'/'.$partHash.'.part';
                    if (FileSystem::exist($blockPath) === false) {
                        return false;
                    }
                    $fileWriter->writeBlock($blockPath, $start, $stop);
                }
                fclose($handle);
                return true;
            }
        }
        return false;
    }

    /**
     * 取消文件上传
     *
     * @param string $tmpPath
     * @param string $id
     * @return boolean
     */
    public static function cancel(string $tmpPath, string $id)
    {
        $savePath = $tmpPath.'/'.$id;
        return FileSystem::delete($savePath);
    }
}
