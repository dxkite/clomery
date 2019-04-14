<?php
namespace support\setting\response;

use suda\framework\Request;
use support\setting\Context;
use support\setting\Visitor;
use support\setting\MenuTree;
use suda\application\Application;
use suda\application\template\RawTemplate;
use suda\application\template\ModuleTemplate;
use suda\application\processor\RequestProcessor;
use suda\framework\Response as FrameworkResponse;
use support\setting\controller\HistoryController;
use support\setting\processor\SettingContextProcessor;

abstract class Response implements RequestProcessor
{
    /**
     * 环境
     *
     * @var Context
     */
    protected $context;

    /**
     * 设置模板信息
     *
     * @var ModuleTemplate
     */
    protected $template;

    /**
     * 历史记录
     *
     * @var HistoryController
     */
    protected $history;

    /**
     * 响应
     *
     * @var FrameworkResponse
     */
    protected $response;

    /**
     * 请求
     *
     * @var Request
     */
    protected $request;

    /**
     * 应用引用
     *
     * @var Application
     */
    protected $application;
    
    /**
     * 访问者
     *
     * @var Visitor
     */
    protected $visitor;

    public function onRequest(Application $application, Request $request, FrameworkResponse $response)
    {
        $this->context = (new SettingContextProcessor)->onRequest($application, $request, $response);
        $this->history = new HistoryController;
        $this->visitor = $this->context->getVisitor();
        $this->application = $application;
        $this->response = $response;
        $this->request = $request;
        $response->setHeader('cache-control', 'no-store');
        if ($this->context->getVisitor()->isGuest()) {
            return $this->onGuestVisit($request);
        } else {
            return $this->onUserVisit($request);
        }
    }

    abstract public function onGuestVisit(Request $request);
    abstract public function onAccessVisit(Request $request);

    public function onUserVisit(Request $request)
    {
        if ($this->context->getVisitor()->canAccess([$this, 'onAccessVisit'])) {
            $this->history->log($this->context->getSession()->id(), $request, $this->context->getVisitor()->getId());
            $view = $this->onAccessVisit($request);
        } else {
            $view = $this->onDeny($request);
        }
        if ($view instanceof RawTemplate) {
            $menuTree = new MenuTree($this->context);
            $menu = $menuTree->getMenu($request->getAttribute('route'));
            $view->set('menuTree', $menu);
            $view->set('currentUser', $this->visitor->getAttributes());
            foreach ($menu as $value) {
                if ($value['select']) {
                    $view->set('title', $value['name']);
                    $view->set('menuName', $value['name']);
                    foreach ($value['child'] as $key => $submenu) {
                        if ($submenu['select']) {
                            $view->set('submenu', $submenu['name']);
                        }
                    }
                }
            }
        }
        return $view;
    }

    public function onDeny(Request $request)
    {
        $menuTree = new MenuTree($this->context);
        $menu = $menuTree->getMenu($request->getAttribute('route'));
        return $this->view('deny')->set('menuTree', $menu);
    }

    /**
     * 获取模板
     *
     * @param string $name
     * @return ModuleTemplate
     */
    public function view(string $name):ModuleTemplate
    {
        return $this->context->getApplication()->getTemplate($name, $this->context->getRequest());
    }

    /**
     * 跳转到某路由
     *
     * @param string $name
     * @param array $parameter
     * @param boolean $allowQuery
     * @param string $default
     * @return void
     */
    public function goRoute(string $name, array $parameter = [], bool $allowQuery = true, ?string $default = null)
    {
        $url = $this->getUrl($name, $parameter, $allowQuery, $default);
        return $this->context->getResponse()->redirect($url);
    }

    /**
     * 获取URL
     *
     * @param string $name
     * @param array $parameter
     * @param boolean $allowQuery
     * @param string|null $default
     * @return string
     */
    public function getUrl(string $name, array $parameter = [], bool $allowQuery = true, ?string $default = null)
    {
        $default = $default ?: $this->context->getApplication()->getRunning()->getFullName();
        return $this->context->getApplication()->getUrl($this->context->getRequest(), $name, $parameter, $allowQuery, $default);
    }
    
    /**
     * 跳转到某页面
     *
     * @param string $url
     * @return void
     */
    public function redirect(string $url)
    {
        $this->context->getResponse()->redirect($url);
    }

    /**
     * 语言翻译
     *
     * @param string $message
     * @param mixed ...$_
     * @return string
     */
    public function _(string $message, ...$_)
    {
        return $this->context->getApplication()->_($message, ...$_);
    }

    /**
     * 跳转到某页面
     *
     * @param string $url
     * @return void
     */
    public function goBack(string $default)
    {
        $url = $this->history->last($this->context->getSession()->id(), 0, $default);
        $this->context->getResponse()->redirect($url);
    }

    /**
     * 去除某个参数跳转当前url
     *
     * @param array|string $key
     * @return void
     */
    public function goThisWithout($key)
    {
        $get = $this->request->get();
        if (\is_string($key)) {
            $key = [$key];
        }
        foreach ($key as $name) {
            unset($get[$name]);
        }
        $this->goRoute($this->request->getAttribute('route'), $get);
    }
}
