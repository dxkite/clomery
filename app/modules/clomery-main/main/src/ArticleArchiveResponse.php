<?php
namespace dxkite\clomery\main\response;

use dxkite\support\visitor\Context;
use dxkite\article\TemplateCSPLoader;
use dxkite\support\visitor\response\Response;
use dxkite\clomery\main\provider\ArchiveProvider;

class ArticleArchiveResponse extends Response
{
    use TemplateCSPLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArchiveProvider;
        $date = request()->get('date');
        $pageCurrent = request()->get('page',1);
        $dateShow = \date_create_from_format('Y-m', $date)->format(__('Y年m月d日'));
        $data = $provider->getListByDate($date, $pageCurrent);
        $page = $this->page('article-archive');
        $page->set('title',__('归档') . $dateShow .' | dxkite 的博客');
        $page->set('articles', $data->getRows());
        $page->set('date',['show'=>$dateShow, 'raw'=>$date]);
        $page->set('page',$data->getPage());
        return $page;
    }
}
