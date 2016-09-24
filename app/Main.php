<?php
class Main
{
    function main()
    {
        View::set('title','主页 - 三人行，必有我师焉。');
        // 测试SQL
        $data=(new Query('SELECT * FROM `#{users}` LIMIT 3;'))->fetchAll();
        var_dump($data);
    }
}