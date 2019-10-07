<?php

namespace support\upload\response;

use suda\framework\Request;
use suda\framework\Response;
use suda\application\Resource;
use support\upload\UploadUtil;
use suda\application\Application;
use suda\framework\filesystem\FileSystem;
use suda\application\processor\RequestProcessor;
use suda\application\processor\FileRangeProcessor;

/**
 * 上传文件显示
 */
class UploadImageResponse implements RequestProcessor
{
    /**
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @throws \Exception
     */
    public function onRequest(Application $application, Request $request, Response $response)
    {
        //  /upload/image/hash/100x100.jpg
        $hash = $request->get('hash');
        $type = $request->get('type');
        $options = $request->get('options');
        $resource = new Resource($application->getDataPath().'/upload');
        $path = $resource->getResourcePath($type . '/' . $hash . '.' . $type);
        if ($path) {
            if (FileSystem::isWritable(SUDA_PUBLIC . '/upload')) {
                $savePath = SUDA_PUBLIC . '/upload/image/' . $type . '/' . $hash . '/' . $options;
                FileSystem::make(dirname($savePath));
                $optionParsed = $this->getOptions($options);
                if (UploadUtil::thumb($path, $savePath, ...$optionParsed)) {
                } else {
                    FileSystem::copy($path, $savePath);
                }
                (new FileRangeProcessor($savePath))->onRequest($application, $request, $response);
            } else {
                (new FileRangeProcessor($path))->onRequest($application, $request, $response);
            }
        } else {
            $response->status(404);
        }
    }

    public function getOptions(string $option): array
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
        return [$width, $height, $size, $quality, $cut];
    }
}
