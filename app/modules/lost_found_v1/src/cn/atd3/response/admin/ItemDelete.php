<?php
namespace cn\atd3\response\admin;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\{User,LostFound};

/**
* visit url /admin/item/{id:int}/delete as all method to run this class.
* you call use u('admin_delete_item',Array) to create path.
* @template: default:admin/item_delete.tpl.html
* @name: admin_delete_item
* @url: /admin/item/{id:int}/delete
* @param: id:int,
*/
class ItemDelete extends \cn\atd3\Adminstrator
{
    public function onRequest(Request $request)
    {
        
        return $this->json(['return'=>LostFound::delete($request->get()->id(0))]);
    }
}
