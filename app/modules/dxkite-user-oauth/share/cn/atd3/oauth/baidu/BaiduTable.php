<?php
namespace cn\atd3\oauth\baidu;
use suda\archive\Table;

class BaiduTable extends Table {
    
    public function __construct(){
        parent::__construct('baidu_auth');
    }

    public function onBuildCreator($table){
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('user', 'bigint', 20)->unsigned()->key()->comment('内部UID'),
            $table->field('uid', 'bigint', 20)->unsigned()->key()->comment('百度UID'),
            $table->field('uname', 'varchar',255)->comment('百度ID'),
            $table->field('portrait', 'varchar',255)->comment('百度头像'),
            $table->field('access_token', 'varchar',255),
            $table->field('refresh_token', 'varchar', 255)->key(),
            $table->field('scope', 'varchar', 255),
            $table->field('expires_in', 'int', 11)->unsigned()
        );
    }
}