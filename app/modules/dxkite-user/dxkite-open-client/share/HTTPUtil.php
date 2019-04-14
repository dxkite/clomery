<?php
namespace dxkite\openclient;

class HTTPUtil
{
    public static function get(string $url):?array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($curl);
        $info = \curl_getinfo($curl);
        if ($errno = curl_errno($curl)) {
            $error_message = curl_strerror($errno);
            curl_close($curl);
            throw new \Exception("cURL error ({$errno}):\n {$error_message}", $errno);
        }
        curl_close($curl);
        if (is_string($data)) {
            return json_decode($data, true);
        }
        return null;
    }
}
