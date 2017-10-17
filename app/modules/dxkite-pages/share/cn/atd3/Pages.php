<?php
namespace cn\atd3;

use suda\core\Response;
use suda\core\Request;
use suda\core\Router;
use suda\core\Context;
use cn\atd3\dao\PagesDAO;
use suda\template\Manager;

class Pages
{
    private static $router;

    /**
     * 加载静态页面配置
     *
     * @return void
     */
    public static function load()
    {
        return (new PagesDAO)->list();
    }

    /**
     * 获取页面链接
     *
     * @param string $name
     * @return void
     */
    public static function page(string $name)
    {
        return u('pages:static_'.$name);
    }

    /**
     * 添加页面
     *
     * @param string $name 页面名
     * @param string $template 模板文件
     * @return void
     */
    public static function add(string $name, string  $match, string $template, array $data)
    {
        return (new PagesDAO)->add($name, $match, $template, $data);
    }

    /**
     * 渲染页面
     *
     * @param string $page
     * @param array $data
     * @return void
     */
    public static function render(string $page, Response $response)
    {
        $page=preg_replace('/^(?:.+?:)static_(.+)$/', '$1', $page);
        $data=self::getPageDate($page);
        $template=$data['template'];
        $values=$data['data'];
        return Manager::display($template)->response($response)->assign($values)->render();
    }

    public static function getPageDate(string $page)
    {
        return (new PagesDAO)->get($page);
    }

    public function addRouter(string $name, string $match)
    {
        self::$router->addRouter('static_'.$name, '/'.$match, 'cn\\atd3\\response\\PagesResponse', 'dxkite/pages');
    }

    public function init($router)
    {
        self::$router=$router;
        $pages=self::load();
        if (is_array($pages)) {
            foreach ($pages as $page) {
                self::addRouter($page['name'], $page['match']);
            }
        }
    }
    
    public static function getTemplate(int $id)
    {
        return (new PagesDAO)->getTemplate($id);
    }

    public static function update(int $id, array $value)
    {
        return (new PagesDAO)->updateByPrimaryKey($id, $value);
    }

    public static function getNew()
    {
        return (new PagesDAO)->getNew();
    }
    public static function staticBase()
    {
        list($admin_prefix, $prefix)=Router::getModulePrefix('dxkite/pages');
        return Request::getInstance()->baseUrl(). trim($prefix, '/');
    }

    public static function template($template)
    {
        $template->addCommand('page', function ($exp) {
            return '<?php echo cn\atd3\Pages::page'.$exp.'; ?>';
        });
        $template->addCommand('page_base', function () {
            return '<?php echo cn\atd3\Pages::staticBase(); ?>';
        });
    }

    public static function getTemplateInfo(string $name)
    {
        return new TemplateInfo($name);
    }

    public static function getHtmlPath(string $name)
    {
        $url=self::page($name);
        $path=preg_replace('/^'.preg_quote(request()->baseUrl(), '/').'/', '', $url);
        if (!preg_match('/\.html$/', $path)) {
            $path = trim($path, '/').'/index.html';
        }
        return APP_PUBLIC.'/'.$path;
    }

    public static function saveHtml(int $id, $response)
    {
        $data=(new PagesDAO)->getById($id);
        $template=$data['template'];
        $values=$data['data'];
        $html=Manager::display($template)->response($response)->assign($values)->getRenderedString();
        $path=self::getHtmlPath($data['name']);
        storage()->mkdirs(dirname($path));
        return storage()->put($path, $html);
    }
}
