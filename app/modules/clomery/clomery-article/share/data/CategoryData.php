<?php
namespace clomery\article\data;

use JsonSerializable;
use suda\application\database\DataObject;
use support\openmethod\RequestInputTrait;
use support\openmethod\MethodParameterInterface;


/**
 * @table category
 * @field id bigint(20) primary unsigned auto
 * @field name varchar(255) unique comment("名称")
 * @field slug varchar(255) unique comment("缩写")
 * @field image varchar(255) comment("图标")
 * @field-serialize description text comment("描述")
 * 
 * @field count int(11) key comment("数量")
 * @field parent bigint(20) key comment("父级")
 * @field index varchar(255) key comment("索引")
 * @field order int(11) key comment("排序")
 * 
 * @field user bigint(20) unsigned key comment("创建用户")
 * @field time int(11) key comment("创建时间")
 */
class CategoryData  extends DataObject implements MethodParameterInterface, JsonSerializable
{
    use RequestInputTrait;
}

