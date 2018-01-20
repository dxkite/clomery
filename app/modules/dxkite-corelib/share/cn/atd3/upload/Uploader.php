<?php
namespace cn\atd3\upload;

use suda\core\Query;
use cn\atd3\upload\dao\UploadTable;
use cn\atd3\upload\dao\UploadDataTable;

class Uploader implements \JsonSerializable
{
    protected $url;
    protected $visibility;
    protected $password;
    protected $mark;
    protected $file;
    protected $user;
    protected $status;

    const PUBLIC_PATH=APP_PUBLIC.'/assets/upload';
    const PROTECTED_PATH=DATA_DIR.'/upload';
    
    const FILE_PUBLIC=0;
    const FILE_SIGN=1;
    const FILE_PASSWORD=2;
    const FILE_PROTECTED=3;

    protected $savePath;
    protected $id;
    protected $passwordHash;


    public function __construct(int $user, File $file, string $mark='resource')
    {
        $this->file=$file;
        $this->user=$user;
        $this->status=UploadTable::FILE_UNUSED;
        $this->mark=$mark;
    }

    /**
     * 设置描述字段
     *
     * @param string $mark
     * @return void
     */
    public function setMark(string $mark)
    {
        $this->mark=$mark;
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
        if ($this->visibility==self::FILE_PUBLIC) {
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
        return $this->visibility==self::FILE_PUBLIC;
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
        if ($this->visibility==self::FILE_PUBLIC) {
            $format=conf('setting.public_file_format', '{yy}/{mm}/{dd}/{time}-{rand:6}');
            $t = time();
            $d = explode('-', date("Y-y-m-d-H-i-s"));
            $format = str_replace("{yyyy}", $d[0], $format);
            $format = str_replace("{yy}", $d[1], $format);
            $format = str_replace("{mm}", $d[2], $format);
            $format = str_replace("{dd}", $d[3], $format);
            $format = str_replace("{hh}", $d[4], $format);
            $format = str_replace("{ii}", $d[5], $format);
            $format = str_replace("{ss}", $d[6], $format);
            $format = str_replace("{time}", $t, $format);
            $randNum = rand(1, getrandmax()) . rand(1, getrandmax());
            if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
                $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
            }
            $this->savePath=$this->file->getType().'/'.$format.'.'.$this->file->getType();
        } else {
            $hash=md5_file($this->file->getPath());
            $this->savePath=$this->file->getType().'/'.$hash.'.'.$this->file->getType();
        }
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
        return u('corelib:upload', ['id'=>$this->id]);
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
            $hash=md5_file($this->file->getPath());
            // 唯一数据ID
            if ($get=$data->select(['id','path'], ['hash'=>$hash])->fetch()) {
                $dataId=$get['id'];
            } else {
                $dataId=$data->insert(['hash'=>$hash, 'ref'=>0,'path'=>$this->getSavePath()]);
            }
            // 同一个用户未使用的同一个文件则继续返回
            if ($get=$upload->select(['id'], ['user'=>$this->user,'data'=>$dataId,'status'=>$upload::FILE_UNUSED])->fetch()) {
                $uploadId = $get['id'];
                $upload->uploadByPrimaryKey($uploadId, [
                    'mark'=>$this->mark,
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
                    'mark'=>$this->mark,
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
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return false;
        }
        $this->id=$uploadId;
        $this->file->move($this->getSaveFullPath());
        return true;
    }

    public function getFile()
    {
        return $this->file;
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
        if ($uploadTableData['visibility']==self::FILE_PUBLIC) {
            $uploadTableData['path'] =self::PUBLIC_PATH.'/'.$uploadDataTableData['path'];
        } else {
            $uploadTableData['path']= self::PROTECTED_PATH.'/'.$uploadDataTableData['path'];
        }
        $instance=new self($uploadTableData['user'], File::createFromArray($uploadTableData), $uploadTableData['mark']) ;
        $instance->savePath=$uploadDataTableData['path'];
        $instance->passwordHash=$uploadTableData['password'];
        $instance->visibility=intval($uploadTableData['visibility']);
        $instance->id=$uploadTableData['id'];
        return $instance;
    }

    public function jsonSerialize()
    {
        return [
            'url'=>$this->getUrl(),
            'id'=>$this->id,
        ];
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }
}
