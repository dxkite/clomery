<?php
class Main
{
    function main()
    {
        View::set('title','管理页面 - 三人行，必有我师焉。');
        // 测试SQL
        // $data=(new Query('SELECT * FROM `#{users}` LIMIT 3;'))->fetchAll();
        $markdown_text="
- uno
- dos
- tres";
        View::set('markdown_text',$markdown_text);
        // var_dump($data);
    }
}