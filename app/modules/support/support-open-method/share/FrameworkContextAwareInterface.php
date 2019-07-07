<?php
namespace support\openmethod;

use suda\framework\Request;
use suda\framework\Response;
use suda\application\Application;

/**
 * 环境感知接口
 */
interface FrameworkContextAwareInterface
{
    /**
     * 环境感知
     *
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @param \suda\framework\Response $response
     * @return void
     */
    public function setContext(Application $application, Request $request, Response $response);
}
