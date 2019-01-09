<?php
namespace dxkite\friendlink\table;

/**
 * 友链表
 */
class FriendLinkTable extends \suda\archive\Table
{
    public function __construct(string $name=null)
    {
        parent::__construct('friend_link');
    }
    
    public function onBuildCreator($table)
    {
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('name', 'varchar', 255)->comment('网站名'),
            $table->field('link', 'varchar', 255)->comment('链接'),
            $table->field('image', 'bigint')->null()->comment('网站LOGO'),
            $table->field('sort', 'int', 11)->default(0)->comment('排序'),
            $table->field('status', 'int', 11)->default(0)->comment('状态')
        );
    }
}
