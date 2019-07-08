<?php
namespace support\openmethod\parameter;

use ArrayIterator;
use ReflectionMethod;
use IteratorAggregate;
use suda\framework\Request;
use suda\application\Application;
use support\openmethod\parameter\File;
use support\openmethod\MethodParameterBag;
use support\openmethod\MethodParameterInterface;
use support\openmethod\processor\ResultProcessor;

/**
 * 表单文件数组
 */
class FileArray implements IteratorAggregate, MethodParameterInterface
{

    /**
     * 文件集合
     *
     * @var File[]
     */
    protected $files = [];

    /**
     * 创建参数
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \support\openmethod\MethodParameterBag $bag
     * @return mixed
     * @throws \Exception
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag)
    {
        $parameter = new self;
        $request = $bag->getRequest();
        $options = $request->post($name, '');
        $ignoreError = preg_match('/ignore\s+error/i', $options)?true:false;
        $onlyImage = preg_match('/only_*image/i', $name)?true:false;
        if (\preg_match('/prefix:(\w+)/i', $options, $match)) {
            $prefix = $match[1];
        } else {
            $prefix = '';
        }
        foreach ($request->getFiles() as $name => $upload) {
            try {
                if (strlen($prefix) > 0 && strpos($name, $prefix) !== 0) {
                    continue;
                }
                $name = substr($name, strlen($prefix));
                $file = new File($upload);
                if ($onlyImage) {
                    if ($file->isImage()) {
                        $parameter->files[$name] = $file;
                    }
                } else {
                    $parameter->files[$name] = $file;
                }
            } catch (\Exception $e) {
                if ($ignoreError == false) {
                    throw  $e;
                }
            }
        }
        return $parameter;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->files);
    }
}
