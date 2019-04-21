<?php
namespace clomery\article\data;

use JsonSerializable;
use suda\application\database\DataObject;
use support\openmethod\RequestInputTrait;
use support\openmethod\MethodParameterInterface;


/**
 * @table tag
 * @field id bigint(20) primary unsigned auto
 * @field name varchar(255) unique comment("名称")
 * @field image varchar(255) comment("图标")
 * @field-serialize description text comment("描述")
 * @field count int comment("数量")
 * @field user bigint(20) unsigned key comment("创建用户")
 * @field index int(11) key comment("排序")
 * @field time int(11) key comment("创建时间")
 */
class TagData  extends DataObject implements MethodParameterInterface, JsonSerializable
{
    use RequestInputTrait;
}
