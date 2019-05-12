<?php
namespace support\setting\provider;

use suda\framework\Request;
use suda\framework\Response;
use support\setting\Context;
use support\setting\Visitor;
use support\session\UserSession;
use suda\application\Application;
use support\setting\exception\UserException;
use support\openmethod\FrameworkContextAwareTrait;
use support\openmethod\FrameworkContextAwareInterface;
use support\setting\processor\SettingContextProcessor;

class UserSessionAwareProvider implements FrameworkContextAwareInterface
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
     * 用户会话分组
     *
     * @var string
     */
    protected $group = 'system';

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
        $this->setBaseContext($application, $request, $response);
        $this->context = new Context($application, $request, $response);
        $vp = new VisitorProvider();
        $this->session = UserSession::createFromRequest($request, $this->getGroup(), $application->conf("app.debug-key", ''));
        $this->visitor = $vp->getVisitor($this->session);
        $this->context->setVisitor($this->visitor);
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
        $this->session = UserSession::createFromRequest($this->request, $this->group, $context->getApplication()->conf("app.debug-key", ''));
        $this->visitor = $context->getVisitor();
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return UserSession
     */
    public function getSession(): UserSession
    {
        return $this->session;
    }

    /**
     * @param UserSession $session
     */
    public function setSession(UserSession $session): void
    {
        $this->session = $session;
    }

    /**
     * 设置会话用户组别
     * @param string $group
     */
    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }
}
