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
class Msg extends \suda\core\Response
{
    protected $mc;
    protected $uc;
    protected $uid;
    protected $request;
    public function onRequest(Request $request)
    {
        $this->mc=new MsgCenter;
        $this->uc=new UserCenter;
       
        $this->client=$request->getHeader('API-Client', $request->cookie('client', $request->get()->client(null)));
        $this->token=$request->getHeader('API-Token', $request->cookie('token', $request->get()->token(null)));


        $this->request=$request;
        
        if ($this->client && $this->token) {
            if (!$this->uc->checkClient(intval($this->client), $this->token)) {
                return $this->json(['error'=>'client is not available!']);
            }
        } else {
            return $this->json(['error'=>'no api client!']);
        }


        $this->uid=(new User($this->uc))->getUserId();

        $action=$request->get()->action;
        $help=array(
            'send'=>[
                'params'=> ['message', 'to'=>'int', 'type'=>['int', MsgCenter::TYPE_MESSAGE]],
                'comments'=>'发送私信消息',
            ] ,
            'inbox'=>[
                'params'=> ['type'=>['int', MsgCenter::TYPE_MESSAGE],'page'=>['int',1], 'count'=>['int',10]],
                'comments'=>'获取消息列表',
            ] ,
            'delete'=>[
                'params'=> ['ids'=>'array'],
                'comments'=>'删除消息列表',
            ] ,
        );

        try {
            // param values array
            $data=$request->isJson()?new Value($request->json()):($request->isPost()?$request->post():$request->get());
            
            switch ($action) {
                case 'inbox':
                    if (!$this->uid) {
                        throw new ApiException('NoUserException', 'please Login');
                    }
                    return $this->json(['return'=>$this->mc->inbox($this->uid, $data->type(MsgCenter::TYPE_MESSAGE), $data->page(1), $data->count(10))]);
                case 'send':
                   // todo ：发送信息的类型修改
                    if (!$this->uid) {
                        throw new ApiException('NoUserException', 'please Login');
                    }
                    Api::check($data, ['message', 'to'=>'int', 'type'=>['int', MsgCenter::TYPE_MESSAGE]]);
                    return $this->json(['return'=>$this->mc->send($this->uid, $data->to, $data->type, $data->message)]);
                case 'delete':
                    if (!$this->uid) {
                        throw new ApiException('NoUserException', 'please Login');
                    }
                    Api::check($data, ['ids'=>'array']);
                    return $this->json(['return'=>$this->mc->delete($this->uid, $data->ids)]);
                default:return $this->json($help);
            }
        } catch (ApiException $e) {
            return $this->json($e);
        } catch (\Exception $e) {
            return $this->json([ 'Exception'=>$e->getMessage()]);
        }
        return $this->json($help);
    }
}
