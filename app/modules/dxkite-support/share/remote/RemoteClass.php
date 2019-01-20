<?php
namespace dxkite\support\remote;

use CURLFile;
use Exception;
use dxkite\support\remote\Config;
use dxkite\support\remote\RemoteException;

/**
 * 远程服务器接口类
 */
class RemoteClass
{
    /**
     * 调用ID
     *
     * @var integer
     */
    protected $id = 0;
    /**
     * 远程URL
     *
     * @var string
     */
    protected $url;
    /**
     * 请求附加头部信息
     *
     * @var array
     */
    protected $headers;
    
    /**
     * 配置
     *
     * @var Config
     */
    protected $config;

    /**
     * 响应码
     *
     * @var int
     */
    protected $responseCode;
    /**
     * 响应文本
     *
     * @var string
     */
    protected $response;
    /**
     * 响应内容类型 
     *
     * @var string
     */
    protected $responseContentType;

    /**
     * 创建远程服务对象接口
     *
     * @param string $url
     * @param string $cookiePath
     * @param array $headers
     */
    public function __construct(string $url, Config $config, array $headers=[])
    {
        $this->url=$url;
        $this->headers = $headers;
        $this->config = $config;
    }
    
    /**
     * 通常调用 方式
     *
     * @param string $method
     * @param array $params
     * @return void
     */
    public function __call(string $method, array $params)
    {
        return $this->exec($this->url, $method, $params, $this->headers);
    }

    /**
     * 含有文件调用方式
     *
     * @param string $method
     * @param array $params
     * @return void
     */
    public function _call(string $method, array $params)
    {
        return $this->exec($this->url, $method, $params, $this->headers);
    }

    /**
     * 调用远程接口
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * @return void
     */
    public function exec(string $url, string $method, array $params, array $headerArray)
    {
        $this->id++; 

        $headers =[
            'XRPC-Id:'. $this->id,
            'XRPC-Method:'.$method,
            'User-Agent: XRPC-Client',
            'Accept: application/json , image/*'
        ];
        
        foreach ($headerArray as $name=>$value) {
            $headers[]=$name.':'.$value;
        }
        $postFile = false;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->config->getCookiePath());
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->config->getCookiePath());
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        
        if ($this->config->getEnableProxy()) {
            curl_setopt($curl, CURLOPT_PROXY, $this->config->getProxyHost());
            curl_setopt($curl, CURLOPT_PROXYPORT, $this->config->getProxyPort());
        }

        if ($this->config->getEnableSSLVerify()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->config->getSSLVerifyHost());
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->config->getSSLVerifyPeer());
        }
        
        foreach ($params as $name => $param) {
            if ($param instanceof \CURLFile) {
                $postFile =true;
            }
        }

        if ($postFile) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        } else {
            $json=json_encode($params);
            $length=strlen($json);
            $headers[]= 'Content-Type: application/json';
            $headers[]=  'Content-Length: '.  $length;
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $this->response = curl_exec($curl);
        $this->responseContentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        if (strlen($this->response) > 0) {
            curl_close($curl);
            if ($this->responseCode  == 200) {
                if (preg_match('/json/i', $this->responseContentType)) {
                    $ret= json_decode($this->response, true);
                    if (is_array($ret) && array_key_exists('error', $ret)) {
                        $error=$ret['error'];
                        if ($error['name'] === 'PermissionDeny') {
                            throw (new RemoteException($error['message'], $error['code']))->setName($error['name']);
                        }
                        throw (new RemoteException($error['message'], $error['code']))->setName($error['name']);
                    }
                    return $ret;
                } else {
                    return $this->response;
                }
            } elseif ($this->responseCode == 500) {
                if (preg_match('/json/i', $this->responseContentType)) {
                    $error=json_decode($this->response, true);
                    throw (new RemoteException($error['error']['message'], $error['error']['code']))->setName($error['error']['name']);
                }
                throw new RemoteException('Server 500 Error');
            }
        } else {
            if ($errno = curl_errno($curl)) {
                $error_message = curl_strerror($errno);
                curl_close($curl);
                throw (new RemoteException("cURL error ({$errno}):\n {$error_message}", $errno))->setName('CURLError');
            }
        }
        return null;
    }

    /**
     * Get 响应码
     *
     * @return  int
     */ 
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Get 响应内容类型
     *
     * @return  string
     */ 
    public function getResponseContentType()
    {
        return $this->responseContentType;
    }

    /**
     * Get 响应文本
     *
     * @return  string
     */ 
    public function getResponse()
    {
        return $this->response;
    }
}
