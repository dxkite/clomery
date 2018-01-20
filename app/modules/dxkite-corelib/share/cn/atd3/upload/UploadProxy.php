<?php
namespace cn\atd3\upload;

use cn\atd3\proxy\ProxyObject;
use cn\atd3\visitor\Context;
use cn\atd3\upload\File;
use suda\core\Storage;

class UploadProxy extends ProxyObject
{
    const STATE_VERIFY=0;
    const STATE_PUBLISH=1;
    const STATE_PROTECTED=2;
    const STATE_PRIVATE=3;
    const STATE_DELETE=4;

    const FILE_PUBLIC=0;
    const FILE_SIGN=1;
    const FILE_PASSWORD=2;
    
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function save(File $file, string $mark, int $status, int $visibility, string $password=null)
    {
        $uploader=new Uploader($this->getUserId(), $file, $mark);
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

    public function delete(int $id)
    {
    }

    public function getFileUrl(int $id, string $password=null)
    {
    }

    public function getFile(int $id, string $password=null)
    {
        $uploader=Uploader::newInstanceById($id);
        if ($uploader) {
            if ($uploader->isPublic()) {
                return $uploader->getFile();
            } elseif ($uploader->getStatus() === Uploader::FILE_SIGN) {
                if (!$this->getContext()->getVisitor()->isGuest()) {
                    return $uploader->getFile();
                }
            } elseif ($uploader->getStatus() === Uploader::FILE_PROTECTED) {
                if (!$this->getContext()->getVisitor()->isGuest()) {
                    $id=$this->getContext()->getVisitor()->getId();
                    if ($uploader->isOwner($id)) {
                        return $uploader->getFile();
                    }
                }
            } elseif ($uploader->getStatus() === Uploader::FILE_PASSWORD) {
                if ($password && $uploader->checkPassword($password)) {
                    return $uploader->getFile();
                }
            }
        }
        return false;
    }
}
