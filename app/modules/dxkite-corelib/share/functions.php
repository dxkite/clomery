<?php

function proxy(string $tableName)
{
    return cn\atd3\proxy\ProxyInstance::new($tableName);
}


function context() {
    return cn\atd3\visitor\Context::getInstance();
}

function visitor() {
    return context()->getVisitor();
}