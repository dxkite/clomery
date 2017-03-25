<?php
namespace cn\atd3\response\storage;

// use namespace
use suda\core\Request;
// database query
use suda\core\Query;
// site cookie
use suda\core\Cookie;
// site session
use cn\atd3\Session;
use cn\atd3\UserCenter;
use cn\atd3\Api;
use cn\atd3\User;
use cn\atd3\ApiException;
use cn\atd3\Token;
use suda\tool\Value;
/**
* visit url /v1.0/storage/upload as POST method to run this class.
* you call use u('storage_upload',Array) to create path.
* @template: default:v1/storage/upload.tpl.html
* @name: storage_upload
* @url: /v1.0/storage/upload
* @param: 
*/
class Upload extends \suda\core\Response
{
    protected $client;
    protected $token;
    protected $uc;
    protected $request;
    public function onRequest(Request $request)
    {
         $this->uc=new UserCenter;
        
        $this->client=$request->getHeader('API-Client',$request->get()->client(null));
        $this->token=$request->getHeader('API-Token',$request->get()->token(null));

        $this->request=$request;
        
        if ($this->client && $this->token) {
            if (!$this->uc->checkClient(intval($this->client),$this->token)) {
                return $this->json(['error'=>'client is not available!']);
            }
        } else {
            return $this->json(['error'=>'no api client!']);
        }
        $id=User::getUserId();
        if ($id===0){
             return $this->json(['error'=>'no user avalable!']);
        } 

        $return=[];
        foreach($request->files() as $name => $file){
            if ($file['error']===0){
                $id=\cn\atd3\File::upload($id,$file['tmp_name'],$file['name'],$file['size']);
                $return[$name]=u('storage_get',['id'=>$id]);
            }elseif ($name) {
                $return[$name]='error';
            }
        }
        return $this->json(['return'=>$return]);
    }
}
