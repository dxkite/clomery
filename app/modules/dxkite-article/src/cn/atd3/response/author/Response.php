<?php
/**
 * Suda FrameWork
 *
 * An open source application development framework for PHP 7.0.0 or newer
 * 
 * Copyright (c)  2017 DXkite
 *
 * @category   PHP FrameWork
 * @package    Suda
 * @copyright  Copyright (c) DXkite
 * @license    MIT
 * @link       https://github.com/DXkite/suda
 * @version    since 1.2.4
 */

namespace cn\atd3\response\author;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /author[/{id:int}] as all method to run this class.
* you call use u('author',Array) to create path.
* @template: default:author/.tpl.html
* @name: author
* @url: /author[/{id:int}]
* @param: id:int,
*/
class Response extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        $page=$this->page('author/profile');
 
        $id=$request->get()->id(0);
 

        return $page->render();
    }
}
