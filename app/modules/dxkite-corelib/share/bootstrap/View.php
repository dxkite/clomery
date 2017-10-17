<?php
namespace bootstrap;

use suda\core\Hook;

class View
{
    public static function page($template)
    {
        $now=$template->get('page.now');
        $max=$template->get('page.max', false);
        $router=$template->get('page.router');
        
        if ($max!==false) {
            $max=$max<=0?1:$max;
            return Component::page($now, $max, $router);
        } else {
            return Component::pageUnknowMax($now, $router, $template->get('page.next', true));
        }
    }
}
