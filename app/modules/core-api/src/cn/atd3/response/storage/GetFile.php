<?php
namespace cn\atd3\response\storage;

// use namespace
use suda\core\Request;
// database query
use suda\core\Query;
// site cookie
use suda\core\Cookie;
// site session
use suda\core\Session;

/**
* visit url /v1.0/storage/{id:int} as GET method to run this class.
* you call use u('storage_get',Array) to create path.
* @template: default:v1/storage/get_file.tpl.html
* @name: storage_get
* @url: /v1.0/storage/{id:int}
* @param: id:int,
*/
class GetFile extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        // params if had
        $id=$request->get()->id(0);
        
         if ($get=\cn\atd3\Upload::get($id)) {
            $hash=$get['hash'];
            $type=$get['type'];
            if (isset($request->get()->download)){
                header('Content-Disposition:attachment;filename='.$get['name']);
            }
            $path=$root=\cn\atd3\File::path($hash);
            if(file_exists($path)){
                $this->file($path,$type,$get['size']);
                return;
            }
            
        }
        $this->state(400);
        exit(0);
    }
}
