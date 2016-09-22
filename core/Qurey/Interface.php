<?php
// 数据库通用接口
interface Qurey_Interface{
    const FETCH_ASSOC=PDO::FETCH_ASSOC;
    const FETCH_BOTH=PDO::FETCH_BOTH;
    const FETCH_NUM=PDO::FETCH_NUM;
    function __construct(string $qurey,array $binds=[]);
    public function fetch(int $fetch_style = self::FETCH_ASSOC);
    public function fetchAll(int $fetch_style = self::FETCH_ASSOC);
    public function values(array $values);
    public function error();
    public function erron();
}