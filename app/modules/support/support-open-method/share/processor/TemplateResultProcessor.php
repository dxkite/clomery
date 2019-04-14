<?php
namespace support\openmethod\processor;

use suda\framework\Request;
use suda\framework\Response;
use suda\application\Application;
use suda\application\template\RawTemplate;

/**
 * 响应处理
 */
class TemplateResultProcessor implements ResultProcessor
{
    /**
     * RawTemplate
     *
     * @var RawTemplate
     */
    protected $template;

    public function __construct(RawTemplate $template)
    {
        $this->template = $template;
    }
    
    public function processor(Application $application, Request $request, Response $response)
    {
        $response->sendContent($this->template);
    }
}
