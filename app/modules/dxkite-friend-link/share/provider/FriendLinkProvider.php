<?php
namespace dxkite\friendlink\provider;

use dxkite\friendlink\controller\FriendLinkController;
use dxkite\support\file\File;

class FriendLinkProvider
{
    public function getFriendLinks():?array
    {
        return (new FriendLinkController)->getFriendLinks();
    }
    
    /**
     * 添加友链
     * @acl friend-link.add
     * @param File|null $logo
     * @param string $name
     * @param string $link
     * @param integer $sort
     * @return integer
     */
    public function addLink(?File $logo, string $name, string $link,int $sort=0):int
    {
       return (new FriendLinkController)->addLink($logo,$name,$link,$sort);
    }

    /**
     * 编辑友链
     * @acl friend-link.edit
     * @param integer $id
     * @param File|null $logo
     * @param string $name
     * @param string $link
     * @param integer $sort
     * @return integer
     */
    public function updateLink(int $id, ?File $logo, string $name, string $link,int $sort=0):int
    {
        return (new FriendLinkController)->updateLink($id,$logo,$name,$link,$sort);
    }

    /**
     * 删除友链
     *
     * @acl friend-link.delete
     * @param integer $id
     * @return integer
     */
    public function delete(int $id):int
    {
        return (new FriendLinkController)->delete($id);
    }
}

