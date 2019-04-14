<?php
namespace dxkite\openclient\provider;

use suda\framework\Request;
use suda\framework\Response;
use support\setting\Context;
use support\setting\Visitor;
use support\session\UserSession;
use suda\application\Application;
use dxkite\openclient\processor\UserInfoProcessor;
use support\openmethod\FrameworkContextAwareTrait;
use support\openmethod\FrameworkContextAwareInterface;

class VisitorAwareProvider implements FrameworkContextAwareInterface
{
    use FrameworkContextAwareTrait {
        setContext as setBaseContext;
    }
    
    /**
     * 用户会话
     *
     * @var UserSession
     */
    protected $session;

    /**
     * 环境
     *
     * @var Context
     */
    protected $context;

    /**
     * 访问者
     *
     * @var Visitor
     */
    protected $visitor;

    /**
     * 登陆分组
     *
     * @var string
     */
    protected $group = 'openuser';
    
    /**
     * 环境感知
     *
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @param \suda\framework\Response $response
     * @return void
     */
    public function setContext(Application $application, Request $request, Response $response)
    {
        $processor = new UserInfoProcessor;
        $this->setBaseContext($application, $request, $response);
        $this->context = $processor->onRequest($application, $request, $response);
        $this->session = UserSession::createFromRequest($request, $this->group);
        $this->visitor = $this->context->getVisitor();
    }

    /**
     * 从环境中载入
     *
     * @param \support\setting\Context $context
     * @return void
     */
    public function loadFromContext(Context $context)
    {
        $this->context = $context;
        $this->setBaseContext($context->getApplication(), $context->getRequest(), $context->getResponse());
        $this->session = UserSession::createFromRequest($this->request, $this->group);
        $this->visitor = $context->getVisitor();
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
        return $this->response->redirect($url);
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
        $default = $default ?: $this->application->getRunning()->getFullName();
        return $this->application->getUrl($this->request, $name, $parameter, $allowQuery, $default ?? $this->request->getAttribute('group'));
    }
}
