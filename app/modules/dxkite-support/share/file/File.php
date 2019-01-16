<?php
namespace dxkite\support\file;

use Exception;
use suda\core\Response;
use dxkite\support\visitor\response\ResponseObject;
use dxkite\support\visitor\response\MethodParameter;

class File implements \JsonSerializable, MethodParameter, ResponseObject
{
    protected $name;
    protected $path;
    protected $size;
    protected $type;
    protected $mimeType;
    protected $isImage = null;
    protected $content;

    private $delete=null;
    private $upload=false;
    private $error;
    private $hash=null;

    public static $errorCode=[
        UPLOAD_ERR_OK =>'OK',
        UPLOAD_ERR_INI_SIZE => 'the uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'the uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'no file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR =>'missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'can not write file',
        UPLOAD_ERR_EXTENSION => 'upload stopped by extension',
    ];

    public function __construct(?string $path,bool $autoDelete=false)
    {
        $this->path=$path;
        if ($path && storage()->exist($path)) {
            $this->name=pathinfo($path, PATHINFO_BASENAME);
            $this->type=strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $this->size=filesize($path);
            $this->delete = $autoDelete?$path:null;
        }
    }

    public function getMd5()
    {
        if (is_null($this->hash) && storage()->exist($this->path)) {
            $this->hash = md5_file($this->path);
        }
        return $this->hash;
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
    
    public function getMimeType()
    {
        return $this->mimeType ?? Response::mime($this->type);
    }
    
    public function setPath(string $path)
    {
        $this->path=$path;
        return $this;
    }
    
    public function setContent(string $content)
    {
        $this->content=$content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function jsonSerialize()
    {
        $jsonData =  [
            'name'=>$this->name,
            'type'=>$this->type,
            'mime'=>$this->getMimeType(),
            'size'=>$this->size,
        ];
        if($this->error) {
            $jsonData['error'] = $this->error;
        }
        if ($this->path) {
            $jsonData['path'] = $this->path;
        } else {
            $jsonData['content'] = \base64_encode($this->content);
            $jsonData['encode'] = 'base64';
        }
        return $jsonData;
    }

    public function isImage()
    {
        return $this->isImage;
    }
    
    public function saveTo(string $path)
    {
        storage()->mkdirs(dirname($path));
        if ($this->upload) {
            if ($this->isImage) {
                if (!$this->conventImage($path)) {
                    move_uploaded_file($this->path, $path);
                }
            } else {
                move_uploaded_file($this->path, $path);
            }
        } else {
            $this->path?storage()->copy($this->path, $path):storage()->put($this->content, $path);
        }
        return storage()->exist($path);
    }

    /**
     * 格式化图片
     *
     * @param string $to
     * @return void
     */
    public function conventImage(string $path)
    {
        if (in_array($this->type, ['jpeg','png','bmp','gif'])) {
            $createImage = 'imagecreatefrom' . $this->type;
            $conventImage = 'image' . $this->type;
            $image = $createImage($this->path);
            if ($this->type === 'jpeg') {
                $convent=$conventImage($image, $path, 100);
            } else {
                $convent=$conventImage($image, $path);
            }
            if ($convent) {
                imagedestroy($image);
            } else {
                return false;
            }
        } elseif ($this->upload) {
            move_uploaded_file($this->path, $path);
        } else {
            storage()->copy($this->path, $path);
        }
        return true;
    }

    public static function createFromArray(array $param)
    {
        $file=new File($param['path']??null);
        $file->content = $param['content']??null;
        $file->name = $param['name'];
        $file->type = $param['type'] ?? strtolower(pathinfo($param['name'], PATHINFO_EXTENSION));
        $file->size = $param['size'];
        return $file;
    }
    
    public static function createFromJson($jsonData):?object
    {
        if (is_array($jsonData)) {
            return self::createFromArray($jsonData);
        }
        return null;
    }

    public static function createFromPost(string $name, $jsonData = []):?object
    {
        $param = request()->files($name);

        if (is_null($param) || !is_uploaded_file($param['tmp_name'])) {
            throw new Exception(__('$0 is not a uploaded file', $name));
        }

        if ($param['error'] != UPLOAD_ERR_OK) {
            throw new Exception(__('$0 upload error $1', $name, static::$errorCode[$param['error']]));
        }

        $type = strtolower(pathinfo($param['name'], PATHINFO_EXTENSION));
        $mime = $param['type'];
        $isImage = null;

        if (preg_match('/image\/*/i', $mime) || in_array($type, ['swf','jpc','jbx','jb2','swc'])) {
            $imageType = false;
            if (function_exists('exif_imagetype')) {
                $imageType = exif_imagetype($param['tmp_name']);
            } else {
                $value = getimagesize($param['tmp_name']);
                if ($value) {
                    $imageType=$value[2];
                }
            }
            if ($imageType) {
                $mime = image_type_to_mime_type($imageType);
                $type = image_type_to_extension($imageType, false);
                $isImage = true;
            } else {
                throw new Exception(__('$0 is not a image file', $name));
            }
        } else {
            $isImage = false;
        }
        
        $file=new File($param['tmp_name']);
        $file->name=$param['name'];
        $file->type= $type;
        $file->mimeType= $mime;
        $file->size=$param['size'];
        $file->upload=true;
        $file->isImage = $isImage;
        $file->delete=storage()->abspath($param['tmp_name']);
        return $file;
    }
    
    public function makeResponse($response)
    {
        if (request()->isJson()) {
            $responseData = $this->jsonSerialize();
            $responseData['path'] = null;
            $data = $response->buildResponse($responseData);
            $response->json($data);
        } else {
            $response->type($this->type);
            if ($this->path) {
                $response->file($this->path);
            } else {
                $response->type($this->type);
                $response->setHeader('Cache-Control: max-age=0');
                $response->setHeader('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                $response->setHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                $response->setHeader('Cache-Control: cache, must-revalidate');
                $response->setHeader('Pragma: public');
                $response->send($this->content);
            }
        }
    }

    public static function create(string $name, string $type, string $content)
    {
        $file=new File(null);
        $file->name=$name;
        $file->content = $content;
        $file->type= $type;
        $file->size=strlen($content);
        return $file;
    }

    public static function createFromBase64(string $type, string $base64)
    {
        $content=base64_decode($base64);
        $file=new File(null);
        $file->content = $content;
        $file->name=md5($content);
        $file->type=$type;
        $file->size=strlen($content);
        return $file;
    }

    public function __destruct()
    {
        if ($this->delete && file_exists($this->delete)) {
            debug()->trace('delete upload tmpfile > '.$this->delete);
            unlink($this->delete);
        }
    }
}
