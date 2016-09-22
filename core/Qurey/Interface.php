<?php

interface Qurey_Interface{
    const FETCH_ASSOC=PDO::FETCH_ASSOC;
    const FETCH_BOTH=PDO::FETCH_BOTH;
    const FETCH_NUM=PDO::FETCH_NUM;
    function __construct(string $qurey,array $binds=[]);
}