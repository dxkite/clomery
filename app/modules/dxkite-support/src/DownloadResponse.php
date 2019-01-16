<?php
namespace dxkite\support\response;

use suda\tool\ZipHelper;
use dxkite\support\visitor\Context;
use dxkite\support\provider\DatabaseProvider;
use dxkite\support\provider\TemplateProvider;
use dxkite\support\visitor\response\Response;

class DownloadResponse extends Response
{
    /**
     *
     * @acl tempalte.download
     * @param Context $context
     * @return void
     */
    public function onVisit(Context $context)
    {
        $name=request()->get('name');
        if ($name) {
            $file = null;
            if (request()->hasGet('template')) {
                $file = (new TemplateProvider)->download($name);
            } elseif (request()->hasGet('database')) {
                $file = (new DatabaseProvider)->download($name);
            }
            if ($file) {
                $this->sendFile($file);
                return;
            }
        }
        hook()->exec('suda:system:error::404');
        return;
    }
}
