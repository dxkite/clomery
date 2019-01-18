<?php
namespace dxkite\clomery\main\response;

use dxkite\support\visitor\Context;
use dxkite\article\TemplateCSPLoader;
use dxkite\support\visitor\response\Response;
use dxkite\clomery\main\provider\ArchiveProvider;

class ArchiveResponse extends Response
{
    use TemplateCSPLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArchiveProvider;
        $data = $provider->getArchive();
        $page = $this->page('archive');
        $page->set('title',__('文章归档') .' | dxkite 的博客');
        $page->set('archives', $data->getRows());
        return $page;
    }
}
