<?php
namespace support\visitor\provider;

use Exception;
use ReflectionException;
use suda\database\exception\SQLException;
use suda\framework\Request;
use suda\framework\Response;
use support\openmethod\AuthorizationInterface;
use support\openmethod\Permission;
use support\visitor\Context;
use support\visitor\Visitor;
use support\session\UserSession;
use suda\application\Application;
use support\openmethod\FrameworkContextAwareTrait;
use support\openmethod\FrameworkContextAwareInterface;

class UserSessionAwareProvider implements FrameworkContextAwareInterface, AuthorizationInterface
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
     * @var string
     */
    protected $tokenName;

    /**
     * 环境感知
     *
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @return void
     * @throws ReflectionException
     * @throws SQLException
     * @throws Exception
     */
    public function setContext(Application $application, Request $request, Response $response)
    {
        $this->setBaseContext($application, $request, $response);
        $this->context = new Context($application, $request, $response);
        $this->session = UserSession::createFromRequest($request, $this->getTokenFrom(), $this->group, $application->conf("app.debug-key", ''));
        $this->visitor = $this->createVisitor($this->session->getUserId());
        $this->context->setVisitor($this->visitor);
    }

    /**
     * @return string
     */
    protected function getTokenFrom() {
        if (strlen($this->tokenName) == 0) {
            return 'x-'.$this->group.'-token';
        }
        return $this->tokenName;
    }

    /**
     * @param string $userId
     * @return Visitor
     * @throws SQLException
     */
    public function createVisitor(string $userId) {
        $vp = new VisitorProvider();
        return $vp->createVisitor($userId);
    }

    /**
     * 从环境中载入
     *
     * @param Context $context
     * @return $this
     * @throws SQLException
     */
    public function loadFromContext(Context $context)
    {
        $this->context = $context;
        $this->setBaseContext($context->getApplication(), $context->getRequest(), $context->getResponse());
        $this->session = UserSession::createFromRequest($this->request, $this->getTokenFrom(), $this->group, $context->getApplication()->conf("app.debug-key", ''));
        $this->visitor = $context->getVisitor();
        return $this;
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

    /**
     * @return Permission
     */
    public function getPermission(): Permission
    {
        return $this->visitor->getPermission();
    }
}
