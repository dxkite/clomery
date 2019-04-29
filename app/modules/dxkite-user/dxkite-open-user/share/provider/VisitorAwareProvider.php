<?php
namespace dxkite\openuser\provider;

use support\setting\provider\UserSessionAwareProvider;

class VisitorAwareProvider extends  UserSessionAwareProvider
{

    protected  $group = 'openuser';

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
        $this->response->redirect($url);
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
