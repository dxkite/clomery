<?php
namespace cn\atd3\upload;

class File implements \JsonSerializable
{
    private $name;
    private $path;
    private $size;
    private $type;
    private $error;
    private $delete=null;
    private $upload=false;
    
    public function __construct(string $path)
    {
        $this->path=$path;
        $this->name=$name;
        $this->type=strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $this->size=filesize($path);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSize()
    {
        return $this->size;
    }
        
    public function getPath()
    {
        return $this->path;
    }
    public function getError()
    {
        return $this->error;
    }

    public function setError(int $error)
    {
        $this->error=$error;
        return $this;
    }
    
    public function setName(string $name)
    {
        $this->name=$name;
        return $this;
    }

    public function setType(string $type)
    {
        $this->type=$type;
        return $this;
    }

    public function setSize(int $size)
    {
        $this->size=$size;
        return $this;
    }
        
    public function setPath(string $path)
    {
        $this->path=$path;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'path'=>$this->path,
            'name'=>$this->name,
            'type'=>$this->type,
            'size'=>$this->size,
            'error'=>$this->error,
        ];
    }

    public function move(string $path)
    {
        storage()->mkdirs(dirname($path));
        if ($this->upload) {
            move_uploaded_file($this->path, $path);
        } else {
            storage()->move($this->path, $path);
        }
        return file_exists($path);
    }

    public static function createFromArray(array $param)
    {
        $file=new File($param['path']);
        $file->name=$param['name'];
        $file->type=strtolower(pathinfo($param['name'], PATHINFO_EXTENSION));
        $file->size=$param['size'];
        return $file;
    }

    public static function createFromPost(string $name)
    {
        if (!is_uploaded_file($name)) {
            new Exception(__('%s is not a uploaded file', $name));
        }
        $param=$_FILES[$name];
        $file=new File($param['tmp_name']);
        $file->name=$param['name'];
        $file->type=strtolower(pathinfo($param['name'], PATHINFO_EXTENSION));
        $file->size=$param['size'];
        $file->upload=true;
        $file->delete=storage()->abspath($param['tmp_name']);
        return $file;
    }

    public static function createFromBase64(string $name, string $base64)
    {
        $path=tempnam(sys_get_temp_dir(), 'base64_upload');
        $content=base64_decode($base64);
        file_put_contents($path, $content);
        $file=new File($path);
        $file->name=$name;
        $file->type=strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $file->size=strlen($content);
        $file->delete=storage()->abspath($path);
        return $file;
    }

    public function __destruct()
    {
        if ($this->delete && file_exists($this->delete)) {
            unlink($this->delete);
        }
    }
}
