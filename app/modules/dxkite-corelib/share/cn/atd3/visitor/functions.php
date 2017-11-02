<?php
function proxy(string $tableName)
{
    return cn\atd3\visitor\ProxyInstance::new($tableName);
}
