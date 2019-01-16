<?php
namespace dxkite\user\provider;

use dxkite\user\controller\UserController;

class UserProvider
{
    protected $controller;
    public function __construct()
    {
        $this->controller = new UserController;
    }

    /**
     * 获取用户信息
     *
     * @acl
     * @param integer $userId
     * @return array|null
     */
    public function getPublicInfo(?int $userId):?array
    {
        $data = $this->controller->getPublicInfo($userId ?? \get_user_id());
        if ($data) {
            $avatar = $data['avatar'];
            $data['avatar']=[];
            $data['avatar']['id'] =  $avatar ;
            $data['avatar']['link'] = u('support:upload', $avatar);
            return $data;
        }
        return null;
    }

    /**
     * 获取账号ID
     * 
     * @acl
     * @param string $account
     * @return integer|null
     */
    public function getAccountId(string $account):?int
    {
        return $this->controller->getAccountId($account);
    }
    
    /**
     * 获取用户公开信息
     *
     * @open false
     * @param array $userIds
     * @return array|null
     */
    public function getPublicInfoArray(array $userIds):?array
    {
        $infoArrayData = $this->controller->getPublicInfoArray($userIds);
        $infoArray = [];
        foreach ($infoArrayData as $data) {
           $avatar = $data['avatar'];
           $data['avatar']=[];
           $data['avatar']['id'] =  $avatar ;
           $data['avatar']['link'] = u('support:upload', $avatar);
           $infoArray[$data['id']] = $data;
        }
        return $infoArray;
    }
}
