<?php
namespace dxkite\android\report\table;

class Report extends \suda\archive\Table
{
    public function __construct()
    {
        parent::__construct('report');
    }

    protected function onBuildCreator($table)
    {
        $table->fields(
            $table->field('id', 'bigint')->primary()->auto(),
            $table->field('name', 'varchar', 32)->comment('称呼'),
            $table->field('message', 'text'),
            $table->field('contact', 'text'),
            $table->field('token', 'varchar', 32)->comment('日志文件权限'),
            $table->field('ip', 'varchar', 32)->comment('IP'),
            $table->field('time', 'int', 11)->key(),
            $table->field('user', 'bigint')->null()->key(),
            $table->field('status', 'int', 11)->key()
        );
        return $table;
    }

    public function token2id(string $token)
    {
        $id= $this->select(['id'], ['token'=>$token])->fetch();
        if ($id) {
            return $id['id'];
        }
        return false;
    }

    public function _inputContactField($contact){
        return serialize($contact);
    }

    public function _outputContactField($contact){
        return unserialize($contact);
    }
}
