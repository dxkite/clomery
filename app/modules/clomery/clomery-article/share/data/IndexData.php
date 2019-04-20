<?php
namespace clomery\article\data;

use JsonSerializable;
use suda\application\database\DataObject;
use support\openmethod\RequestInputTrait;
use support\openmethod\MethodParameterInterface;


/**
 * 目录索引数据
 * 
 * @field id bigint(20) primary unsigned auto
 * @field count int(11) key comment("数量")
 * @field parent bigint(20) key comment("父级")
 * @field index varchar(255) key comment("索引")
 * @field order int(11) key comment("排序")
 */
class IndexData  extends DataObject implements MethodParameterInterface, JsonSerializable
{
    use RequestInputTrait;
}

