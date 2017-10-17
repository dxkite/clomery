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

namespace cn\atd3\response\article\tag;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /tag as all method to run this class.
* you call use u('tag',Array) to create path.
* @template: default:article/tag.tpl.html
* @name: tag
* @url: /tag
* @param: 
*/
class TagResponse extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        $page=$this->page('dxkite/article:1.0.0:article/tag');

        // params if had
        ;
        // param values array
        $value=array();
        // display template

        $page->set('title', 'Welcome to use Suda!')
        ->set('helloworld', 'Hello,World!')
        ->set('value', $value);

        return $page->render();
    }
}
