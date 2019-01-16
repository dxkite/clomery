<?php
namespace dxkite\support\file;

class Response extends \suda\core\Response
{
    public $allowGetFile = true;

    public function onRequest(\suda\core\Request $request)
    {
        $fileId=$request->get('id', 0);
        if ($fileId) {
            hook()->exec('upload:download::check', [$this,$fileId]);
            if ($this->allowGetFile) {
                if (isset($request->get()->pwd)) {
                    $file=proxy('media')->getFile($fileId, $request->get()->pwd);
                } else {
                    $file=proxy('media')->getFile($fileId);
                }
                if ($file && storage()->exist($file->getPath())) {
                    return $this->displayFile($file->getPath(), $file->getName(), $file->getType());
                }
            } else {
                hook()->execIf('system:http_error', [403]);
                return;
            }
        }
        hook()->execIf('suda:system:error::404');
    }

    /**
    *  直接输出文件
    */
    public function displayFile(string $path, string $filename=null, string $type=null, bool $download=false)
    {
        $content=file_get_contents($path);
        $hash   = md5($content);
        $size   = strlen($content);
        if (!$this->ifMatchETag($hash)) {
            $type   = $type ?? pathinfo($path, PATHINFO_EXTENSION);
            $filename=$filename ?? pathinfo($path, PATHINFO_BASENAME);

            $this->type($type);

            self::setHeader('Content-Length:'.$size);
            self::setHeader('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            self::setHeader('Pragma: public'); // HTTP/1.0

            if ($download) {
                self::setHeader('Content-Disposition: attachment;filename="'.$filename.'.'.$type.'"');
                self::setHeader('Cache-Control: max-age=0');
                self::setHeader('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                self::setHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            }
            echo $content;
        }
    }
}
