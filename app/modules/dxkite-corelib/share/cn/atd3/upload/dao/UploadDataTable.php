<?php
namespace cn\atd3\upload\dao;

use suda\archive\Table;
use suda\core\Query;

class UploadDataTable extends Table
{
    public function __construct()
    {
        parent::__construct('upload_data');
    }

    public function onBuildCreator($table) 
    {
        return $table->fields(
            $table->field('id','bigint',20)->primary()->unsigned()->auto(),
            $table->field('hash','varchar',32)->unique()->comment("hash"),
            $table->field('path','varchar',255)->comment("相对路径"),
            $table->field('ref','int',11)->comment("引用")
        );
    }
}
