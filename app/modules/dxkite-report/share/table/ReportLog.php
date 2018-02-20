<?php
namespace dxkite\android\report\table;

class ReportLog  extends \suda\archive\Table {
    public function  __construct(){
        parent::__construct('report_log');
    }

    protected function onBuildCreator($table){
        $table->fields(
            $table->field('id','bigint')->primary()->auto(),
            $table->field('report','bigint')->key(),
            $table->field('file','bigint')->key()
        );
        return $table;
    }
}