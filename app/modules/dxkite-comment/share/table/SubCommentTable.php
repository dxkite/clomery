<?php
namespace dxkite\comment\table;

use suda\archive\Table;
use suda\tool\Command;

/**
 * 子评论
 */
class SubCommentTable extends CommentTable
{
    public function __construct($target)
    {
        parent::__construct($target,'sub');
    }

    public function onBuildCreator($table)
    {
        $table = parent::onBuildCreator($table);
        return $table->fields(
            $table->field('parent', 'bigint', 20)->key()->comment('父评论'),
            $table->field('reply', 'bigint', 20)->key()->comment('回复')
        );
    }
}
