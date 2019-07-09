<?php
namespace support\visitor;

use Exception;
use suda\framework\Request;
use suda\framework\Session;
use suda\framework\Response;
use suda\application\Application;
use suda\framework\session\PHPSession;

class Context
{
    /**
     * 请求
     *
     * @var Request
     */
    protected $request;

    /**
     * 响应
     *
     * @var Response
     */
    protected $response;

    /**
     * 应用程序
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

    /**
     * 会话
     *
     * @var Session
     */
    protected $session;

    /**
     * 从响应中创建
     *
     * @param Application $app
     * @param Request $request
     * @param Response $response
     * @throws Exception
     */
    public function __construct(Application $app, Request $request, Response $response)
    {
        $this->application = $app;
        $this->request = $request;
        $this->response = $response;
        $this->session = new PHPSession($request, $response);
    }

    /**
     * Get 应用程序
     *
     * @return  Application
     */
    public function getApplication():Application
    {
        return $this->application;
    }

    /**
     * Set 应用程序
     *
     * @param  Application  $application  应用程序
     *
     * @return  self
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get 响应
     *
     * @return  Response
     */
    public function getResponse():Response
    {
        return $this->response;
    }

    /**
     * Set 响应
     *
     * @param  Response  $response  响应
     *
     * @return  self
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get 请求
     *
     * @return  Request
     */
    public function getRequest():Request
    {
        return $this->request;
    }

    /**
     * Set 请求
     *
     * @param  Request  $request  请求
     *
     * @return  self
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get 访问者
     *
     * @return  Visitor
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * Set 访问者
     *
     * @param  Visitor  $visitor  访问者
     *
     * @return  self
     */
    public function setVisitor(Visitor $visitor)
    {
        $this->visitor = $visitor;

        return $this;
    }

    /**
     * Get 会话
     *
     * @return  Session
     */
    public function getSession():Session
    {
        return $this->session;
    }

    /**
     * @param Visitor $visitor
     */
    public function update(Visitor $visitor) {
        $this->visitor = $visitor;
        try {
            $this->session->update();
        } catch (Exception $e) {
        }
    }

    /**
     * Set 会话
     *
     * @param  Session  $session  会话
     *
     * @return  self
     */
    public function setSession(Session $session)
    {
        $this->session = $session;

        return $this;
    }
}
