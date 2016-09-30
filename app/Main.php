<?php
class Main
{
    function __construct()
    {

    }
    function main()
    {
        View::set('title','管理页面 - 三人行，必有我师焉。');
     // 测试SQL
       $data=(new Query('SELECT * FROM `#{users}` LIMIT 3;'))->fetchAll();
       $head_index=[
           [
               'text'=>'首页',
               'url'=>Page::url('main_page'),
               'select'=>true,
           ],
           [
               'text'=>'文章',
               'url'=>Page::url('main_article'),
           ],
           [
               'text'=>'资源',
           ],
           [
               'text'=>'留言板',
               'url'=>Page::url('notes_page')
           ]
       ];

       View::set('head_index',$head_index);
       View::set('copyright','atd3.cn');
    }
    function article(int $id=0)
    {
       //  var_dump($id);
        View::set('title','文章- '.$id.' - 三人行，必有我师焉。');
    }
}