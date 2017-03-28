<?php
namespace cn\atd3\response;

// use namespace
use suda\core\Request;
// database query
use suda\core\Query;
// site cookie
use suda\core\Cookie;


use cn\atd3\Session;
use cn\atd3\MsgCenter;
use cn\atd3\UserCenter;
use cn\atd3\User;
use cn\atd3\Api;
use cn\atd3\ApiException;
use cn\atd3\Token;
use suda\tool\Value;

/**
* visit url /v1/msg[/{action}] as all method to run this class.
* you call use u('msg_api',Array) to create path.
* @template: default:v1/msg.tpl.html
* @name: msg_api
* @url: /v1/msg[/{action}]
* @param: action:string,
*/
class Msg extends \cn\atd3\ApiAction
{
    protected $mc;
    protected $uid;

    public function action(string $action, Value $data)
    {
        if ($action)
        {
            $this->uid = User::getUserId();
            if (!$this->uid) {
                throw new ApiException('permissionDenied', '用户没登陆！');
            }
            $this->mc= new MsgCenter;
        }
        parent::action($action,$data);
    }

    /**
    * 收信箱
    */
    public function actionInbox(int $type=MsgCenter::TYPE_MESSAGE, int $page=1, int $count=10)
    {
        return $this->mc->inbox($this->uid, $type,  $page, $count);
    }

    /**
    * 发送邮件
    */
    public function actionSend(int $to, string $message, int $type=MsgCenter::TYPE_MESSAGE)
    {
        return $this->mc->send($this->uid, $to, $type, $message);
    }

    /**
    * 删除邮件
    */
    public function actionDelete(array $ids)
    {
        return $this->mc->delete($this->uid, $ids);
    }
}
