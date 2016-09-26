<?php
class Main
{
    function main()
    {
        View::set('title','管理页面 - 三人行，必有我师焉。');
     // 测试SQL
       $data=(new Query('SELECT * FROM `#{users}` LIMIT 3;'))->fetchAll();

       // View::set('md_text',$markdown_text);
        //View::set('rst_text',$rst);
        var_dump($data);
    }
    function article(int $id)
    {
        var_dump($id);
        View::set('title','文章阅读 '.$id.' - 三人行，必有我师焉。');
    }
}