<?php
namespace support\openmethod\parameter;

use SplFileObject;
use ReflectionMethod;
use suda\framework\Request;
use suda\framework\Response;
use suda\application\Application;
use suda\framework\http\UploadedFile;
use support\openmethod\MethodParameterBag;
use support\openmethod\MethodParameterInterface;
use support\openmethod\processor\ResultProcessor;
use suda\application\processor\FileRangeProccessor;

/**
 * 表单文件
 */
class File extends SplFileObject implements ResultProcessor, MethodParameterInterface
{
    /**
     * 是否是图片
     *
     * @var bool
     */
    protected $image;

    /**
     * 扩展名
     *
     * @var string
     */
    protected $extension;

    /**
     * Mime描述符
     *
     * @var string
     */
    protected $mimeType;

    /**
     * 上传的文件名
     *
     * @var string
     */
    protected $originalName;

    public function __construct(UploadedFile $file)
    {
        parent::__construct($file->getTempname());
        $this->mimeType = $file->getMimeType();
        $this->image = $this->guessImage();
        $this->originalName = $file->getOriginalName();
    }

    /**
     * Get 上传的文件名
     *
     * @return  string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * 推测图片类型
     *
     * @return bool
     */
    protected function guessImage():bool
    {
        $type = strtolower($this->getExtension());
        if (preg_match('/image\/*/i', $this->mimeType) || in_array($type, ['swf','jpc','jbx','jb2','swc'])) {
            $imageType = static::getImageTypeIfy($this->getPathname());
            if ($imageType) {
                $this->mimeType = image_type_to_mime_type($imageType);
                $this->extension = image_type_to_extension($imageType, false);
                return true;
            }
        }
        return false;
    }

    /**
     * 获取图片类型
     *
     * @param string $path
     * @return string|null
     */
    public static function getImageTypeIfy(string $path):?string {
        if (function_exists('exif_imagetype')) {
            return exif_imagetype($path);
        } else {
            $value = getimagesize($path);
            if ($value) {
                return $value[2];
            }
        }
        return null;
    }

    /**
     * 获取扩展名
     *
     * @return string
     */
    public function getExtension():string
    {
        return $this->extension ?? parent::getExtension();
    }

    /**
     * 获取是否为图片
     *
     * @return boolean
     */
    public function isImage():bool
    {
        return $this->image;
    }

    /**
     * 输出到响应
     *
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @param \suda\framework\Response $response
     * @return mixed
     */
    public function processor(Application $application, Request $request, Response $response)
    {
        $processor = new FileRangeProccessor($this);
        return $processor->onRequest($application, $request, $response);
    }

    /**
     * 创建参数
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \support\openmethod\MethodParameterBag $bag
     * @return mixed
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag)
    {
        $request = $bag->getRequest();
        if ($request->hasPost('name')) {
            return null;
        }
        return new self($request->getFile($name));
    }
}
