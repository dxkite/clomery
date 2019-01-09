<?php
namespace dxkite\friendlink\view\hook;

use dxkite\friendlink\controller\FriendLinkController;

class FriendLinkRender
{
    protected $links = [];
    protected static $instance = null;

    protected function __construct()
    {
        $this->links = (new FriendLinkController)->getFriendLinks();
    }

  
    public static function compilerHook($template)
    {
        $template->addCommand('friendLink', function () {
            return '<?php dxkite\friendlink\view\hook\FriendLinkRender::friendLinkBlockRender(function(){ ?>';
        });
        $template->addCommand('friendLinkEnd', function () {
            return '<?php }); ?>';
        });
    }

    public static function friendLinkBlockRender($viewCallback)
    {
        if (is_null(static::$instance)) {
            static::$instance = new self;
        }
        ob_start();
        $viewCallback();
        $html = ob_get_clean();
        foreach (self::$instance->links as $link) {
            if (is_null($link['image'])) {
                echo str_replace([
                    '#name',
                    '#link',
                ], [
                    $link['name'],
                    $link['link'],
                ], $html);
            } else {
                echo str_replace([
                    '#name',
                    '#link',
                    '#image'
                ], [
                    $link['name'],
                    $link['link'],
                    u('support:upload', $link['image'])
                ], $html);
            }
        }
    }
}
