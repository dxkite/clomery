<?php
namespace dxkite\openuser\setting\provider;

use suda\orm\TableStruct;
use support\setting\PageData;
use support\session\UserSession;
use support\setting\provider\UserSessionAwareProvider;
use dxkite\openuser\setting\controller\ClientController;

class ClienProvider extends UserSessionAwareProvider
{
    /**
     * UserController
     *
     * @var ClientController
     */
    protected $controller;

    public function __construct()
    {
        $this->controller = new ClientController;
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
        return $this->controller->add($name, $description, $hostname);
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
        return $this->controller->edit($id, $name, $description, $hostname);
    }

    /**
     * 获取一条记录
     *
     * @param string $id
     * @return \suda\orm\TableStruct|null
     */
    public function get(string $id):?TableStruct {
        return $this->controller->get($id);
    }

    /**
     * 删除网站
     *
     * @param string $id
     * @return boolean
     */
    public function delete(string $id):bool
    {
        return $this->controller->delete($id);
    }

    /**
     * 重置密钥
     *
     * @param string $id
     * @return boolean
     */
    public function reset(string $id):bool
    {
        return $this->controller->reset($id);
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
        return $this->controller->list($page, $row);
    }
}
