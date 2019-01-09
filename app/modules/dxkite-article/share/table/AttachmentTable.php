<?php
namespace dxkite\article\table;

use suda\archive\Table;
use suda\tool\Pinyin;
use suda\core\Request;
use suda\core\Query;
use dxkite\support\file\File;
use dxkite\support\file\Media;
use dxkite\support\file\UploadFile;

class AttachmentTable extends Table
{
    const TYPE_ATTACHMEMT=0;
    const TYPE_RESOURCE=1;

    public function __construct()
    {
        parent::__construct('article_attachment');
    }

    public function onBuildCreator($table)
    {
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('aid', 'bigint', 20)->unsigned()->key()->comment("文章ID"),
            $table->field('name', 'varchar', 255)->comment('附件名'),
            $table->field('fid', 'bigint', 20)->unsigned()->key()->comment("文件ID"),
            $table->field('type', 'tinyint', 1)->key()->comment("附件或者资源"),
            $table->field('time', 'int', 11)->key()->comment("时间"),
            $table->field('ip', 'varchar', 32)->comment("IP"),
            $table->field('status', 'tinyint', 1)->key()->comment("状态")
        );
    }
}
