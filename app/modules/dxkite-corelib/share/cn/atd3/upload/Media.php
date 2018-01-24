<?php
namespace cn\atd3\upload;

use cn\atd3\upload\File;

class Media extends UploadProxy
{
    /**
     * POST上传资源文件
     *
     * @param File $file 上传的文件
     * @return integer 资源ID
     */
    public function upload(File $file):int
    {
    }


    /**
     * 获取资源
     *
     * @param integer $id 资源ID
     * @return void
     */
    public function get(int $id)
    {
    }

    /**
     * 获取资源信息
     *
     * @param integer $id 资源ID
     * @return void
     */
    public function info(int $id)
    {
    }

    /**
     * 删除资源
     *
     * @param integer $id 资源ID
     * @return bool 是否删除成功
     */
    public function delete(int $id) : bool
    {
    }

    public function save(File $file, string $mark, int $status, int $visibility, string $password=null)
    {
        if (is_null($password)) {
            $fileinfo=parent::save($file, $mark, $status, $visibility);
        } else {
            $fileinfo=parent::save($file, $mark, $status, $visibility, $password);
        }
        if ($fileinfo) {
            return $fileinfo->getId();
        }
        return false;
    }
}
