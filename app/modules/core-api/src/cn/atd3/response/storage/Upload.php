<?php
namespace cn\atd3\response\storage;

use suda\core\Request;
use suda\tool\Value;
use cn\atd3\ApiResponse;
use cn\atd3\User;

/**
* visit url /v1.0/storage/upload as POST method to run this class.
* you call use u('storage_upload',Array) to create path.
* @template: default:v1/storage/upload.tpl.html
* @name: storage_upload
* @url: /v1.0/storage/upload
* @param:
*/
class Upload extends ApiResponse
{
    public function onRequest(Request $request)
    {
        $this->uid = User::getUserId();
        if (!$this->uid) {
            throw new ApiException('permissionDenied', '用户没登陆！');
        }
        parent::onRequest($request);
        $return=[];
        foreach ($request->files() as $name => $file) {
            if ($file['error']===0) {
                $id=\cn\atd3\File::upload($this->uid, $file['tmp_name'], $file['name'], $file['size']);
                $return[$name]=u('storage_get', ['id'=>$id]);
            } elseif ($name) {
                $return[$name]='error';
            }
        }
        return $this->data($return);
    }
    public function action(string $action, Value $data)
    {
    }
    public function printHelp()
    {
    }
}
