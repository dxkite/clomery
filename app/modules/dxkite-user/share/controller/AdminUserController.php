<?php
namespace dxkite\user\controller;

use dxkite\support\file\File;
use dxkite\user\table\UserTable;
use dxkite\support\view\TablePager;
use dxkite\user\controller\UserController;

class AdminUserController
{
    protected $table;
    protected $controller;
    protected static $fields = ['id','name','avatar','email','mobile','signupTime','status'];
    public function __construct()
    {
        $this->table = new UserTable;
        $this->controller = new UserController;
    }

    public function list(?int $page=1, int $row =10)
    {
        return TablePager::listWhere($this->table->setFields(self::$fields), 'status != :status', ['status' => UserTable::STATUS_DELETE], $page, $row);
    }
    
    public function search(string $field, string $search, ?int $page=1, int $row =10)
    {
        return TablePager::search(
            $this->table->setFields(self::$fields),
            $field,
            $search,
            'status != :status',
            ['status' => UserTable::STATUS_DELETE],
            $page,
            $row
        );
    }

    public function modifyStatus(array $ids, int $status)
    {
        return $this->table->update(['status'=> $status], ['id'=>$ids]);
    }

    public function delete(array $ids)
    {
        return $this->modifyStatus($ids, UserTable::STATUS_DELETE);
    }

    public function get(int $userId):?array
    {
        return $this->table->select(self::$fields, ['id' => $userId])->fetch();
    }

    public function add(string $name, ?string $email, ?string $mobile, string $password):?int
    {
        return $this->controller->add($name, $email, $mobile, $password);
    }

    public function edit(int $id, array $sets)
    {
        return $this->controller->edit($id, $sets);
    }

    public function setAvatar(int $user, File $file)
    {
        return $this->controller->setAvatar($user, $file);
    }
}
