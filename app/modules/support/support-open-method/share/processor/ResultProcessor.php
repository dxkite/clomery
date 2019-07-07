<?php
namespace support\openmethod\processor;

use suda\framework\Request;
use suda\framework\Response;
use suda\application\Application;

/**
 * 响应处理
 */
interface ResultProcessor
{
    public function processor(Application $application, Request $request, Response $response);
}
