<?php
namespace cn\atd3;

use suda\core\Request;
use suda\tool\Value;

abstract class ApiResponse extends \suda\core\Response
{
    protected $client;
    protected $token;
    protected $request;
    protected $uc;
    abstract public function action(string $action, Value $data);
    
    public function onRequest(Request $request)
    {
        $this->client=$request->getHeader('API-Client', $request->cookie('client', $request->get()->client('')));
        $this->token=$request->getHeader('API-Token', $request->cookie('token', $request->get()->token('')));
        $this->request=$request;
        $this->uc=new UserCenter;
        if ($this->client && $this->token) {
            if (!$this->uc->checkClient(intval($this->client), $this->token)) {
                return $this->data(null, 'unknownClient', 'API授权不可用！');
            }
        } else {
            return $this->data(null, 'unknownClient', 'API授权信息丢失！');
        }

        $action=$request->get()->action;
        try {
            // param values array
            $data=$request->isJson()?new Value($request->json()):($request->isPost()?$request->post():$request->get());
            if ($action) {
                $this->action($action, $data);
            } else {
                $this->printHelp();
            }
        } catch (ApiException $e) {
            return $this->json($e);
        } catch (\Exception $e) {
            return $this->data(['input'=>$request->input()], 'unknownException', $e->getMessage());
        }
    }


    public function check($permissions)
    {
        $id=User::getUserId();
        if ($id && $permissions) {
            if (!User::hasPermission($id, $permissions)) {
                throw new ApiException('permissionDenied', '权限不足！', ['id'=>$id, 'permissions'=>$permissions]);
            }
        } else {
            throw new ApiException('permissionDenied', '用户未登录！');
        }
        return true;
    }

    public function data($data, string $error=null, string $message=null)
    {
        return $this->json([
            'error'=>$error,
            'message'=>$message,
            'data'=>$data,
        ]);
    }

    abstract public function printHelp();
}
