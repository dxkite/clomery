<?php
namespace support\upload;

use suda\framework\http\UploadedFile;
use support\session\table\UploadTable;
use suda\framework\filesystem\FileSystem;

/**
 * 块文件写入工具
 */
class BlockFileWriter
{
    /**
     * 创建文件
     *
     * @param string $name
     * @param string $md5
     * @param string $user
     * @param string $ip
     * @return null|array
     */
    public static function create(string $name, string $md5, string $user, string $ip, int $status = UploadTable::UPLOADING)
    {
        $table = new UploadTable;
        $hash = UploadUtil::md5encode($md5);
        if ($data = $table->read(['id'])->where(['hash' => $hash, 'user' => $user])->one()) {
            return [
                'hash' => $hash,
                'id' => $data['id'],
            ];
        }
        if ($id = $table->write([
            'hash' => $hash,
            'name' => pathinfo($name, PATHINFO_BASENAME),
            'ip' => $ip,
            'user' => $user,
            'time' => time(),
            'type' => pathinfo($name, PATHINFO_EXTENSION),
            'status' => $status
        ])->id()) {
            return [
                'hash' => $hash,
                'id' => $id,
            ];
        }
        return null;
    }

    /**
     * 上传分片
     *
     * @param string $tmpPath
     * @param string $id
     * @param BlockFile $file
     * @return array
     */
    public static function upload(string $tmpPath, BlockFile $file)
    {
        $data = $file->getData();
        $id = $file->getId();
        $hash = static::getDataHash($data);
        $file = $hash.'.part';
        $index = 'part.index';
        $savePath = $tmpPath.'/'.$id;
        FileSystem::makes($savePath);
        static::saveDataIndex($savePath.'/'.$index, $file);
        static::saveData($savePath.'/'.$file, $file);
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
            return  UploadUtil::hash($data->getTempname());
        } else {
            return UploadUtil::md5encode(\md5($data));
        }
    }

    /**
     * 保存索引数据
     *
     * @param string $index
     * @param BlockFile $file
     * @return void
     */
    protected static function saveDataIndex(string $index, BlockFile $file)
    {
        $indexFile = \fopen($index, 'wb+');
        if ($indexFile !== false) {
            \fputcsv($indexFile, [$file->getRangeStart(), $file->getRangeStop(), $hash]);
            \fclose($indexFile);
        }
    }

    /**
     * 保存文件数据
     *
     * @param string $path
     * @param BlockFile $file
     * @return void
     */
    protected static function saveData(string $path, BlockFile $file)
    {
        $data = $file->getData();
        if ($data instanceof UploadedFile) {
            FileSystem::move($data->getTempname(), $path);
        } else {
            FileSystem::put($path, $data);
        }
    }

    /**
     * 保存文件
     *
     * @param string $tmpPath
     * @param string $id
     * @param string $savePath
     * @return void
     */
    public static function finish(string $tmpPath, string $id, string $savePath)
    {
        $indexPath = $tmpPath.'/'.$id.'/part.index';
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
