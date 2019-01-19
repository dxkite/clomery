<?php
namespace dxkite\support\visitor\response;

use suda\core\Request;
use suda\core\Exception;
use suda\template\Template;
use dxkite\support\file\File;
use dxkite\support\visitor\Context;
use dxkite\support\visitor\Visitor;
use dxkite\support\visitor\Permission;

abstract class Response extends \suda\core\Response
{
    private $context;
    
    final public function onRequest(Request $request)
    {
        if (conf('database.enable',false) === false) {
            // 配置文件位置 app/resource/config/config.json:databse.enable=true
            throw (new Exception(new \Exception('please enable database config: need (database.enable=true)')))->setName('ConfigError');
        }
        $context=Context::getInstance();
        $this->context=$context;
        $context->setRequest($request);
        if ($context->getVisitor()->canAccess([ $this,'onVisit'])) {
            $response = $this->onVisit($context);
        } else {
            $response =  $this->onDeny($context);
        }
        // 支持JSON输出页面
        if ($response instanceof Template && preg_match('/application\/json/i',$request->getHeader('Accept'))) {
            $this->json($response->get());
            return;
        }
        return $response;
    }
    
    abstract public function onVisit(Context $context);

    public function onDeny(Context $context)
    {
        $route=config()->get('deny_access');
        if ($route) {
            $this->go(u($route));
        } else {
            $this->etag(md5(time()));
            echo '<h1>deny access</h1>';
        }
    }
    
    public function getContext()
    {
        return $this->context;
    }

    public function sendFile(File $file)
    {
        $this->type($file->getType());
        if ($file->getPath()) {
            $this->file($file->getPath());
        } else {
            $this->type($file->getType());
            $this->setHeader('Cache-Control: max-age=0');
            $this->setHeader('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            $this->setHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            $this->setHeader('Cache-Control: cache, must-revalidate');
            $this->setHeader('Pragma: public');
            $this->send($file->getContent());
        }
    }
}
