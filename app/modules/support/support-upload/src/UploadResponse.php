<?php

namespace support\upload\response;

use suda\framework\Request;
use suda\framework\Response;
use suda\application\Resource;
use suda\application\Application;
use suda\application\processor\RequestProcessor;
use suda\application\processor\FileRangeProcessor;

/**
 * 上传文件显示
 */
class UploadResponse implements RequestProcessor
{
    /**
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @return mixed|void
     * @throws \Exception
     */
    public function onRequest(Application $application, Request $request, Response $response)
    {
        //  /upload/image/hash/100x100.jpg
        $path = $request->get('path');
        $type = $request->get('type');
        $options = $request->get('options');
        $resource = new Resource($application->getDataPath() . '/upload');
        $path = $resource->getResourcePath($type . '/' . $path);
        if ($path) {
            (new FileRangeProcessor($path))->onRequest($application, $request, $response);
        } else {
            $response->status(404);
        }
    }
}
