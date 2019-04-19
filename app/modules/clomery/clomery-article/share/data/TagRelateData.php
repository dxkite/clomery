<?php
namespace clomery\article\data;

use JsonSerializable;
use suda\application\database\DataObject;
use support\openmethod\RequestInputTrait;
use support\openmethod\MethodParameterInterface;


/**
 * @table tag_relate
 * @field id bigint(20) primary unsigned auto
 * @field relate bigint(20) key comment("名称")
 * @field tag bigint(20) key comment("图标")
 */
class TagRelateData  extends DataObject implements MethodParameterInterface, JsonSerializable
{
    use RequestInputTrait;
}