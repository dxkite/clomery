<?php
namespace dxkite\friendlink\controller;

use dxkite\support\file\File;
use dxkite\support\file\Media;
use dxkite\friendlink\table\FriendLinkTable;

class FriendLinkController
{
    protected $table;

    public function __construct()
    {
        $this->table=new FriendLinkTable;
    }

    public function getFriendLinks():?array
    {
        return $this->table->order('sort')->list();
    }

    public function addLink(?File $logo, string $name, string $link,int $sort=0)
    {
        $data = [
            'name' => $name,
            'link' => $link,
            'sort' => $sort,
        ];
        if ($logo) {
            $file = Media::save($logo);
            if ($file) {
                $data['image'] = $file->getId();
            }
        }
        return $this->table->insert($data);
    }

    public function updateLink(int $id, ?File $logo, string $name, string $link,int $sort=0)
    {
        $data = [
            'name' => $name,
            'link' => $link,
            'sort' => $sort,
        ];
        if ($logo) {
            $file = Media::save($logo);
            if ($file) {
                $get = $this->table->getByPrimaryKey($id);
                Media::delete($get['image']);
                $data['image'] = $file->getId();
            }
        }
        
        return $this->table->updateByPrimaryKey($id, $data);
    }

    public function delete(int $id)
    {
        return $this->table->deleteByPrimaryKey($id);
    }
}
