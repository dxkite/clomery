<?php
namespace dxkite\support\file;

use suda\core\Query;
use dxkite\support\table\file\FileInfoTable as UploadTable;
use dxkite\support\table\file\FileDataTable as UploadDataTable;

class UploadFile implements \JsonSerializable
{
    protected $url;
    protected $visibility;
    protected $password;
    protected $file;
    protected $user;
    protected $status;
    protected $hash;
 
    const PUBLIC_PATH=DATA_DIR.'/upload';
    const PROTECTED_PATH=DATA_DIR.'/upload';
    const STATIC_PATH=APP_PUBLIC.'/assets/upload';
    // 文件可见性
    const FILE_PROTECTED=UploadTable::FILE_PROTECTED; // 登陆保护
    const FILE_PASSWORD=UploadTable::FILE_PASSWORD;  // 密码保护
    const FILE_PUBLISH=UploadTable::FILE_PUBLISH;    // 公开的
    const FILE_PRIVATE=UploadTable::FILE_PRIVATE;   // 私有文件

    // 文件状态
    const IS_UNUSED=0;      // 未使用的
    const IS_NORMAL=1;      // 正常文件
    const IS_DELETED=2;     // 删除的
    const IS_VERIFY=3;      // 待审核文件

    protected $savePath;
    protected $id;
    protected $passwordHash;


    public function __construct(int $user, File $file)
    {
        $this->file=$file;
        $this->user=$user;
        $this->hash = $file->getPath()?md5_file($file->getPath()):md5($file->getContent());
        $this->status=UploadTable::IS_UNUSED;
    }

    /**
     * 设置文件状态
     *
     * @param int $status
     * @return void
     */
    public function setStatus(int $status)
    {
        $this->status=$status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isOwner(int $user)
    {
        return $this->user == $user;
    }
    /**
     * 获取保存全路径
     *
     * @return void
     */
    public function getSaveFullPath()
    {
        if ($this->visibility==self::FILE_PUBLISH) {
            return self::PUBLIC_PATH.'/'.$this->getSavePath();
        } else {
            return self::PROTECTED_PATH.'/'.$this->getSavePath();
        }
    }

    /**
     * 是否为公开文件
     *
     * @return boolean
     */
    public function isPublic()
    {
        return $this->visibility==self::FILE_PUBLISH;
    }

    /**
     * 获取文件内容是否需要密码
     *
     * @return void
     */
    public function needPassword()
    {
        return !is_null($this->passwordHash);
    }

    /**
     * 如果有密码则检查密码
     *
     * @param string $password
     * @return void
     */
    public function checkPassword(string $password)
    {
        if (empty($this->passwordHash)) {
            return true;
        }
        return password_verify($password, $this->passwordHash);
    }

    /**
     * 获取上传文件保存的相对路径
     *
     * @return void
     */
    protected function getSavePath()
    {
        if ($this->savePath) {
            return $this->savePath;
        }
        $this->savePath=$this->file->getType().'/'. $this->hash .'.'.$this->file->getType();
        return $this->savePath;
    }

    /**
     *
     * 获取文件URL，
     * 公开文件返回URL
     * 私有文件
     * 否则 false
     *
     * @return void
     */
    public function getUrl()
    {
        if ($type= $this->file->getType()) {
            return u('support:upload', ['id'=>$this->id,'type'=>$this->file->getType()]);
        }
        return u('support:upload', ['id'=>$this->id]);
    }

    /**
     * 获取文件ID，确保文件已经保存
     *
     * @return void
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 设置文件可见性
     *
     * @param int $visibility
     * @param string $password
     * @return void
     */
    public function setVisibility(int $visibility, string $password=null)
    {
        $this->visibility=$visibility;
        if (!empty($password)) {
            $this->visibility=self::FILE_PASSWORD;
            $this->password=$password;
        }
        return $this;
    }

    /**
     * 返回文件可见性
     *
     * @return void
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * 保存上传文件
     *
     * @return void
     */
    public function save()
    {
        try {
            Query::begin();
            $data=new UploadDataTable;
            $upload=new UploadTable;
            // 唯一数据ID
            if ($get=$data->select(['id','path'], ['hash'=>$this->hash])->fetch()) {
                $dataId=$get['id'];
                $this->savePath=$get['path'];
            } else {
                $dataId=$data->insert(['hash'=>$this->hash, 'ref'=>0,'path'=>$this->getSavePath()]);
            }
            // 同一个用户未使用的同一个文件则继续返回
            if ($get=$upload->select(['id'], ['user'=>$this->user,'data'=>$dataId,'status'=>$upload::IS_UNUSED])->fetch()) {
                $uploadId = $get['id'];
                $upload->uploadByPrimaryKey($uploadId, [
                    'time'=>time(),
                    'visibility'=> $this->visibility,
                    'status'=>$this->status,
                ]);
            } else {
                $newData=[
                    'user'=>$this->user,
                    'name'=>$this->file->getName(),
                    'data'=>$dataId,
                    'time'=>time(),
                    'type'=>$this->file->getType(),
                    'size'=>$this->file->getSize(),
                    'visibility'=> $this->visibility,
                    'status'=>$this->status,
                ];
                if ($this->password) {
                    $this->passwordHash=password_hash($this->password, PASSWORD_DEFAULT);
                    $newData['password']=$this->passwordHash;
                }
                $uploadId = $upload->insert($newData);
                // 引用次数+1
                if ($uploadId) {
                    Query::update($data->getTableName(), 'ref = ref + 1', ['id' =>$dataId ]);
                }
            }
            if (!storage()->exist($this->getSaveFullPath())) {
                if (!$this->file->saveTo($this->getSaveFullPath())) {
                    debug()->warning('upload file save error > '.$this->getSaveFullPath());
                    Query::rollBack();
                    return false;
                }
            }
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        $this->id=$uploadId;
        return true;
    }

    public function getFile()
    {
        return $this->file;
    }

    public static function existHash(string $hash):bool
    {
        $data=new UploadDataTable;
        return $data->select(['id'], ['hash'=>$hash])->fetch()?true:false;
    }
    
    public static function exist(File $file):bool
    {
        $hash=md5_file($file->getPath());
        return static::existHash($hash);
    }

    public static function newInstanceById(int $id)
    {
        $upload=new UploadTable;
        $data=new UploadDataTable;
        $uploadTableData=$upload->getByPrimaryKey($id);
        if (!$uploadTableData) {
            return false;
        }
        $uploadDataTableData=$data->getByPrimaryKey($uploadTableData['data']);
        $fileData=$uploadTableData;
        if ($uploadTableData['visibility']==self::FILE_PUBLISH) {
            $fileData['path'] =self::PUBLIC_PATH.'/'.$uploadDataTableData['path'];
        } else {
            $fileData['path']= self::PROTECTED_PATH.'/'.$uploadDataTableData['path'];
        }
        $instance=new self($uploadTableData['user'], File::createFromArray($fileData)) ;
        $instance->savePath=$uploadDataTableData['path'];
        $instance->hash= $uploadDataTableData['hash'];
        $instance->passwordHash=$uploadTableData['password'];
        $instance->visibility=intval($uploadTableData['visibility']);
        $instance->id=$uploadTableData['id'];
        $instance->status=intval($uploadTableData['status']);
        return $instance;
    }

    public function jsonSerialize()
    {
        return [
            'url'=>$this->getUrl(),
            'hash'=>$this->hash,
            'id'=>$this->id,
        ];
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }
}
