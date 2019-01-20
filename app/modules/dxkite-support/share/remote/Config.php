<?php
namespace dxkite\support\remote;

/**
 * 远程配置
 */
class Config
{
    /**
     * 超时设置
     *
     * @var integer
     */
    protected $timeout = 10;
    /**
     * Cookie存储位置
     *
     * @var string
     */
    protected $cookiePath = null;
    /**
     * 开启代理设置
     *
     * @var boolean
     */
    protected $enableProxy =false;
    /**
     * 代理服务器IP
     *
     * @var string
     */
    protected $proxyHost = '127.0.0.1';
    
    /**
     * 代理服务器端口
     *
     * @var integer
     */
    protected $proxyPort = 1080;
    /**
     * 是否开启SSL验证
     *
     * @var boolean
     */
    protected $enableSSLVerify = false;

    /**
     * 对等验证
     *
     * @var boolean
     */
    protected $SSLVerifyPeer = true;
    /**
     * 主机验证
     *
     * @var boolean
     */
    protected $SSLVerifyHost = true;
    /**
     * Get cookie存储位置
     *
     * @return  string
     */
    public function getCookiePath()
    {
        return $this->cookiePath;
    }

    /**
     * Set cookie存储位置
     *
     * @param  string  $cookiePath  Cookie存储位置
     *
     * @return  self
     */
    public function setCookiePath(string $cookiePath)
    {
        $this->cookiePath = $cookiePath;

        return $this;
    }

    /**
     * Get 超时设置
     *
     * @return  integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set 超时设置
     *
     * @param  integer  $timeout  超时设置
     *
     * @return  self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get 代理服务器IP
     *
     * @return  string
     */
    public function getProxyHost()
    {
        return $this->proxyHost;
    }

    /**
     * Set 代理服务器IP
     *
     * @param  string  $proxyHost  代理服务器IP
     *
     * @return  self
     */
    public function setProxyHost(string $proxyHost)
    {
        $this->proxyHost = $proxyHost;

        return $this;
    }

    /**
     * Get 开启代理设置
     *
     * @return  boolean
     */
    public function getEnableProxy()
    {
        return $this->enableProxy;
    }

    /**
     * Set 开启代理设置
     *
     * @param  boolean  $enableProxy  开启代理设置
     *
     * @return  self
     */
    public function setEnableProxy(bool $enableProxy)
    {
        $this->enableProxy = $enableProxy;

        return $this;
    }

    /**
     * Get 是否开启SSL验证
     *
     * @return  boolean
     */ 
    public function getEnableSSLVerify()
    {
        return $this->enableSSLVerify;
    }

    /**
     * Set 是否开启SSL验证
     *
     * @param  boolean  $enableSSLVerify  是否开启SSL验证
     *
     * @return  self
     */ 
    public function setEnableSSLVerify(bool $enableSSLVerify)
    {
        $this->enableSSLVerify = $enableSSLVerify;

        return $this;
    }

    /**
     * Get 代理服务器端口
     *
     * @return  integer
     */ 
    public function getProxyPort()
    {
        return $this->proxyPort;
    }

    /**
     * Set 代理服务器端口
     *
     * @param  integer  $proxyPort  代理服务器端口
     *
     * @return  self
     */ 
    public function setProxyPort($proxyPort)
    {
        $this->proxyPort = $proxyPort;

        return $this;
    }

    /**
     * Get 主机验证
     *
     * @return  boolean
     */ 
    public function getSSLVerifyHost()
    {
        return $this->SSLVerifyHost;
    }

    /**
     * Set 主机验证
     *
     * @param  boolean  $SSLVerifyHost  主机验证
     *
     * @return  self
     */ 
    public function setSSLVerifyHost(bool $SSLVerifyHost)
    {
        $this->SSLVerifyHost = $SSLVerifyHost;

        return $this;
    }

    /**
     * Get 对等验证
     *
     * @return  boolean
     */ 
    public function getSSLVerifyPeer()
    {
        return $this->SSLVerifyPeer;
    }

    /**
     * Set 对等验证
     *
     * @param  boolean  $SSLVerifyPeer  对等验证
     *
     * @return  self
     */ 
    public function setSSLVerifyPeer(bool $SSLVerifyPeer)
    {
        $this->SSLVerifyPeer = $SSLVerifyPeer;

        return $this;
    }
}
