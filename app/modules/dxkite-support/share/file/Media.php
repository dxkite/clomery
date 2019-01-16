<?php
namespace dxkite\support\file;

use suda\core\Query;
use dxkite\support\proxy\ProxyObject;
use dxkite\support\table\file\FileDataTable;
use dxkite\support\table\file\FileInfoTable;

class Media
{
    public static function getFromPost(string $name)
    {
        return File::createFromPost($name);
    }

    /**
     * 保存文件
     *
     * @param File $file
     * @param integer $status
     * @param integer $visibility
     * @param string $password
     * @return void
     */
    public static function save(File $file, int $status=UploadFile::IS_NORMAL, int $visibility=UploadFile::FILE_PUBLISH, string $password=null)
    {
        $uploader=new UploadFile(get_user_id(), $file);
        $uploader->setStatus($status);
        if (is_null($password)) {
            $uploader->setVisibility($visibility);
        } else {
            $uploader->setVisibility($visibility, $password);
        }
        if ($uploader->save()) {
            return $uploader;
        }
        return false;
    }
    
    /**
     * 匿名保存文件
     *
     * @param File $file
     * @param integer $status
     * @param integer $visibility
     * @param string $password
     * @return void
     */
    public static function saveAnonymous(File $file, int $status=UploadFile::IS_NORMAL, int $visibility=UploadFile::FILE_PUBLISH, string $password=null)
    {
        $uploader=new UploadFile(0, $file);
        $uploader->setStatus($status);
        if (is_null($password)) {
            $uploader->setVisibility($visibility);
        } else {
            $uploader->setVisibility($visibility, $password);
        }
        if ($uploader->save()) {
            return $uploader;
        }
        return false;
    }

    /**
     * 保存文件，文件公开查看
     *
     * @param File $file
     * @param integer $status
     * @return UploadFile
     */
    public static function saveFile(File $file, int $status=UploadFile::IS_NORMAL)
    {
        return self::save($file, $status);
    }


    /**
     * 保存登陆保护的文件，登陆后才可以查看
     *
     * @param File $file
     * @param integer $status
     * @return UploadFile
     */
    public static function saveFileProtected(File $file, int $status=UploadFile::IS_NORMAL)
    {
        return self::save($file, $status, UploadFile::FILE_PROTECTED, $password);
    }

    /**
     * 保存文件，并设置密码，使用密码才可以查看
     *
     * @param File $file
     * @param string $password
     * @param integer $status
     * @return UploadFile
     */
    public static function saveFilePassword(File $file, string $password, int $status=UploadFile::IS_NORMAL)
    {
        return self::save($file, $status, UploadFile::FILE_PASSWORD, $password);
    }

    /**
     * 储存私有文件，只允许程序访问
     *
     * @param File $file
     * @param integer $status
     * @return void
     */
    public static function saveFilePrivate(File $file, int $status=UploadFile::IS_NORMAL)
    {
        return self::save($file, $status, UploadFile::FILE_PRIVATE);
    }

    /**
     * 获取文件
     *
     * @open false
     * @param integer $id
     * @param string $password
     * @return File
     */
    public static function getFile(int $id, ?string $password=null):?File
    {
        $uploader=UploadFile::newInstanceById($id);
        if ($uploader) {
            // 只显示正常文件
            if ($uploader->getStatus() !== UploadFile::IS_NORMAL) {
                return null;
            }
            // 直接可以查看文件
            if ($uploader->isPublic()) {
                return $uploader->getFile();
            }
            // 登陆可以查看文件
            elseif ($uploader->getVisibility() === UploadFile::FILE_PROTECTED) {
                if (!visitor()->isGuest()) {
                    return $uploader->getFile();
                }
            }
            // 私有文件
            elseif ($uploader->getVisibility() === UploadFile::FILE_PRIVATE) {
                if (!visitor()->isGuest()) {
                    if ($uploader->isOwner(visitor()->getId())) {
                        return $uploader->getFile();
                    }
                }
            }
            // 使用密码可以查看文件
            elseif ($uploader->getVisibility() === UploadFile::FILE_PASSWORD) {
                if ($password && $uploader->checkPassword($password)) {
                    return $uploader->getFile();
                }
            }
        }
        return null;
    }
    
    public static function getFileUrl(int $id, bool $full=false)
    {
        if ($full) {
            $uploader=UploadFile::newInstanceById($id);
            return $uploader->getUrl();
        }
        return u('support:upload', ['id'=>$id]);
    }

    public static function delete(int $id)
    {
        $upload=(new FileInfoTable)->getByPrimaryKey($id);
        if ($upload) {
            try {
                Query::begin();
                $uploadData=(new FileDataTable)->getByPrimaryKey($upload['data']);
                Query::update((new FileDataTable)->getTableName(), 'ref = ref - 1', ['id' =>$upload['data']]);
                (new FileInfoTable)->deleteByPrimaryKey($id);
                if ($uploadData['ref'] - 1 ==0) {
                    (new FileDataTable)->deleteByPrimaryKey($upload['data']);
                    if ($upload['visibility'] == UploadFile::FILE_PUBLISH) {
                        $path=UploadFile::PUBLIC_PATH.'/'.$uploadData['path'];
                    } else {
                        $path=UploadFile::PROTECTED_PATH.'/'.$uploadData['path'];
                    }
                    storage()->delete($path);
                }
                Query::commit();
            } catch (\Exception $e) {
                Query::rollBack();
                return false;
            }
        }
        return true;
    }
}
