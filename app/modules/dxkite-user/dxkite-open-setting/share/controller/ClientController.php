<?php
namespace dxkite\openuser\setting\controller;

use suda\orm\TableStruct;
use support\setting\PageData;
use support\session\UserSession;
use suda\orm\exception\SQLException;
use dxkite\openuser\table\ClientTable;

class ClientController
{
    /**
     * 用户表
     *
     * @var ClientTable
     */
    protected $table;

    
    public function __construct()
    {
        $this->table = new ClientTable;
    }
    
    /**
     * 添加网站
     *
     * @param string $name
     * @param string $description
     * @param string|null $hostname
     * @return string
     */
    public function add(string $name, string $description, ?string $hostname):string
    {
        $appid = UserSession::encode(\md5($name.\microtime(true), true));
        return $this->table->write([
            'name' => $name,
            'description' => $description,
            'hostname' => $hostname,
            'appid' => $appid,
            'secret' => UserSession::encode(\md5($appid.\microtime(true), true)),
        ])->id();
    }


    /**
     * 获取一条记录
     *
     * @param string $id
     * @return array|null
     */
    public function get(string $id):?array {
        return $this->table->read('*')->where(['id' => $id])->one();
    }

    /**
     * 编辑网站
     *
     * @param string $id
     * @param string $name
     * @param string $description
     * @param string|null $hostname
     * @return boolean
     */
    public function edit(string $id, string $name, string $description, ?string $hostname):bool
    {
        $appid = UserSession::encode(\md5($name.\microtime(true), true));
        return $this->table->write([
            'name' => $name,
            'description' => $description,
            'hostname' => $hostname,
        ])->where(['id' => $id])->ok();
    }

    /**
     * 删除网站
     *
     * @param string $id
     * @return boolean
     */
    public function delete(string $id):bool
    {
        return $this->table->delete(['id' => $id])->ok();
    }

    /**
     * 重置密钥
     *
     * @param string $id
     * @return boolean
     */
    public function reset(string $id):bool
    {
        return $this->table->write([
            'secret' => UserSession::encode(\md5($id.\microtime(true), true)),
        ])->where(['id' => $id])->ok();
    }

    /**
     * 列出网站
     *
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     */
    public function list(?int $page = null, int $row = 10): PageData
    {
        return PageData::create($this->table->read('*'), $page, $row);
    }
}
