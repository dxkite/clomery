<?php

namespace dxkite\support\api;

use dxkite\support\file\File;
use dxkite\support\visitor\exception\CallableException;
use dxkite\support\proxy\exception\ProxyException;

/**
 * 远程服务器接口类
 * @example
 * ```php
 * $url='http://www.atd3.cn/api/v1.0/user/';
 * debug()->time('Method__postTest');
 * $class = new \dxkite\support\api\RemoteClass($url);
 * $file=$class->getCodeImage();
 * var_dump($file);
 * var_dump($file->move('D:\Server\file\savefile.png'));
 * debug()->timeEnd('Method__postTest');
 * ```
 */
class RemoteClass
{
    protected static $id = 0;
    protected $url;
    protected $outputFile = false;
    protected $headers;
    const cookieFileSavePath = DATA_DIR .'/cookie';

    public function __construct(string $url, bool $outputFile =false, array $headers=[])
    {
        $this->url=$url;
        $this->outputFile =$outputFile;
        $this->headers = $headers;
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
        return static::exec($this->url, $method, $params, $this->outputFile, $this->headers);
    }

    /**
     * 含有文件调用方式
     *
     * @param string $method
     * @param array $params
     * @return void
     */
    public function _call(string $method, array $params, bool $outputFile=false)
    {
        return static::exec($this->url, $method, $params, $outputFile, $this->headers);
    }

    /**
     * 调用远程接口
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * @return void
     */
    public static function exec(string $url, string $method, array $params, bool $outputFile=false, array $headerArray)
    {
        self::$id++;
        $cookiePath = self::cookieFileSavePath .'/'.visitor()->getId();
        $cookiePath = storage()->path($cookiePath);
        $cookieFile = $cookiePath  . '/'.context()->getSessionId();
        $headers =[
            'XRPC-Id:'.self::$id,
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
        curl_setopt($curl, CURLOPT_TIMEOUT, conf('remoteClass.timeout', 3));
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, conf('remoteClass.connectTimeout', 3));

        if ($verifyHost = conf('remoteClass.ssl.verifyHost', null)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifyHost);
        }
        if ($verifyPeer = conf('remoteClass.ssl.verifyPeer', null)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifyPeer);
        }

        foreach ($params as $name => $param) {
            if (is_object($param) && get_class($param) == File::class) {
                $postFile =true;
                $params[$name] = '@'.$param->getPath();
            }
        }
        if ($postFile) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        } else {
            $json=json_encode([
                'params'=>$params,
                'method'=>$method,
                'id'=>self::$id
                ]);
            $length=strlen($json);
            $headers[]= 'Content-Type: application/json';
            $headers[]=  'Content-Length: '.  $length;
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($curl);
        $contentType=curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $code =curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSend =curl_getinfo($curl, CURLINFO_HEADER_OUT);
        
        debug()->trace($headerSend);
        debug()->trace($data);

        if ($data) {
            curl_close($curl);
            if ($code  == 200) {
                if (preg_match('/json/i', $contentType)) {
                    $ret= json_decode($data, true);
                    if (array_key_exists('error', $ret)) {
                        $error=$ret['error'];
                        if ($error['name'] === 'PermissionDeny') {
                            throw new ProxyException($error['message'], $error['code']);
                        }
                        throw (new CallableException($error['message'], $error['code']))->setName($error['name']);
                    } elseif (array_key_exists('result', $ret)) {
                        $result= $ret['result'];
                        if (is_array($result) && count($result) ==3 && array_key_exists('binary', $result) && array_key_exists('type', $result) && array_key_exists('encode', $result)) {
                            if ($outputFile) {
                                echo base64_decode($result['binary']);
                                return;
                            } else {
                                return File::createFromBase64($result['type'], $result['binary']);
                            }
                        }
                        return $ret['result'];
                    }
                } else {
                    return $data;
                }
            } elseif ($code =500) {
                if (preg_match('/json/i', $contentType)) {
                    $error=json_decode($data, true);
                    throw (new CallableException($error['error']['message'], $error['error']['code']))->setName($error['error']['name']);
                }
            }
        } else {
            if ($errno = curl_errno($curl)) {
                $error_message = curl_strerror($errno);
                curl_close($curl);
                throw (new CallableException("cURL error ({$errno}):\n {$error_message}", $errno))->setName('CURLError');
            }
        }
        return null;
    }
}
