<?php
namespace dxkite\openuser\response;

use suda\framework\Request;
use support\setting\Context;
use support\setting\Visitor;
use suda\application\Application;
use suda\application\template\RawTemplate;
use suda\application\template\ModuleTemplate;
use dxkite\openuser\processor\UserInfoProcessor;
use suda\application\processor\RequestProcessor;
use suda\framework\Response as FrameworkResponse;

abstract class UserResponse implements RequestProcessor
{
    /**
     * 环境
     *
     * @var Context
     */
    protected $context;

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
        $this->context = (new UserInfoProcessor)->onRequest($application, $request, $response);
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
    abstract public function onUserVisit(Request $request);
    
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
     * 跳转页面
     *
     * @return void
     */
    public function jumpForward()
    {
        $url = $this->request->get('redirect_uri');
        if (strlen($url) > 0) {
            $this->redirect($url);
        } else {
            $route = $this->request->getAttribute('route');
            $home = $this->application->getRouteName('home', $this->application->getRunning()->getFullName());
            if ($route !== $home) {
                $this->goRoute('home');
            }
        }
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
