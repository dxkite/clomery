<?php
namespace support\openmethod\parameter;

use IteratorAggregate;
use suda\framework\Request;
use suda\application\Application;
use support\openmethod\parameter\File;
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
    protected $files;

    /**
     * 从请求中创建文件
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @return mixed
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, Application $application, Request $request)
    {
        $parameter = new self;
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
