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
class UploadResponse implements RequestProcessor
{
    public function onRequest(Application $application, Request $request, Response $response)
    {
        //  /upload/image/hash/100x100.jpg
        $path = $request->get('path');
        $type = $request->get('type');
        $options = $request->get('options');
        $resource = new Resource([ SUDA_DATA.'/upload' ]);
        $path = $resource->getResourcePath($type.'/'.$path);
        if ($path) {
            return (new FileRangeProccessor($path))->onRequest($application, $request, $response);
        }
        $response->status(404);
    }
}
