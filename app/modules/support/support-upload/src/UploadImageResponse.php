<?php
namespace support\upload\response;

use suda\framework\Request;
use suda\framework\Response;
use suda\application\Resource;
use support\upload\UploadUtil;
use suda\application\Application;
use suda\framework\filesystem\FileSystem;
use suda\application\processor\RequestProcessor;
use suda\application\processor\FileRangeProccessor;

/**
 * 上传文件显示
 */
class UploadImageResponse implements RequestProcessor
{
    public function onRequest(Application $application, Request $request, Response $response)
    {
        //  /upload/image/hash/100x100.jpg
        $hash = $request->get('hash');
        $type = $request->get('type');
        $options = $request->get('options');
        $extension = pathinfo($options, PATHINFO_EXTENSION);
        $resource = new Resource([ SUDA_DATA.'/upload' ]);
        $path = $resource->getResourcePath($type.'/'.$hash.'.'.$type);
        if ($path) {
            if (FileSystem::isWritable(SUDA_PUBLIC.'/upload')) {
                $savePath = SUDA_PUBLIC.'/upload/image/'.$type.'/'.$hash.'/'.$options;
                FileSystem::make(dirname($savePath));
                $optionParsed = $this->getOptions($options);
                if (UploadUtil::thumb($path, $savePath, ...$optionParsed)) {
                } else {
                    FileSystem::copy($path, $savePath);
                }
                return (new FileRangeProccessor($savePath))->onRequest($application, $request, $response);
            }
            return (new FileRangeProccessor($path))->onRequest($application, $request, $response);
        }
        $response->status(404);
    }

    public function getOptions(string $option):array
    {
        $width = null;
        $height = null;
        $size = null;
        $quality = null;
        $cut = '';
        $outType = 'jpeg';
        if (\preg_match('/(w|width)(-|_|&)*(\d+)/', $option, $match)) {
            $width = \intval($match[3]);
        }
        if (\preg_match('/(h|height)(-|_|&)*(\d+)/', $option, $match)) {
            $height = \intval($match[3]);
        }
        if (\preg_match('/(\d+)x(\d+)/', $option, $match)) {
            $width = \intval($match[1]);
            $height = \intval($match[2]);
        }
        if (\preg_match('/(s|size)(-|_|&)*(\d+)/', $option, $match)) {
            $size = \intval($match[3]);
        }
        if (\preg_match('/(q|quality)(-|_|&)*(\d+)/', $option, $match)) {
            $quality = \intval($match[3]);
        }
        if (\preg_match('/\.(jpeg|png)$/', $option, $match)) {
            $outType = $match[1];
        }
        return [$width, $height, $size, $quality, $cut, $outType];
    }
}
