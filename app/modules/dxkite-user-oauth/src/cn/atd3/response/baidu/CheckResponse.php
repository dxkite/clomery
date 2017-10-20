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

namespace cn\atd3\response\baidu;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /baidu-checked as all method to run this class.
* you call use u('callback',Array) to create path.
* @template: default:baidu/check.tpl.html
* @name: callback
* @url: /baidu-checked
* @param: 
*/
class CheckResponse extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        $page=$this->page('baidu/check');
        $page->set('title', 'Welcome to use Suda!')
        ->set('helloworld', 'Hello,World!')
        ->set('value', $value);
        return $page->render();
    }
}
