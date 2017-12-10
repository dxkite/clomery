<?php

function proxy(string $tableName)
{
    return cn\atd3\proxy\ProxyInstance::new($tableName);
}
