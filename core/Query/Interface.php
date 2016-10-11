<?php
// 数据库通用接口
interface Query_Interface
{
    const FETCH_ASSOC=PDO::FETCH_ASSOC;
    const FETCH_BOTH=PDO::FETCH_BOTH;
    const FETCH_NUM=PDO::FETCH_NUM;
    public function __construct(string $qurey, array $binds=[]);
    public function fetch(int $fetch_style = self::FETCH_ASSOC);
    public function fetchAll(int $fetch_style = self::FETCH_ASSOC);
    // 返回受影响的行数
    public function exec():int;
    public function values(array $values);
    public function query(string $query, array $array=[]);
    public function error();
    public function erron():int;
    // 事务系列
    public function beginTransaction();
    public function commit();
    public function rollBack();
}
