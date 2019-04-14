<?php
namespace support\setting\response;

use suda\framework\Request;
use support\setting\response\SettingResponse;


class IndexResponse extends SettingResponse
{
    public function onSettingVisit(Request $request)
    {
        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gdinfo = $gd['GD Version'];
        } else {
            $gdinfo = '不支持';
        }
        $upload = ini_get('file_uploads') ? ini_get('upload_max_filesize'):'不支持';
        return $this->view('index')->set('version', [
            'suda' => SUDA_VERSION,
            'php' => PHP_VERSION,
            'server' => $request->getServer('server-software'),
            'gd' => $gdinfo,
          
        ])->set('upload', $upload);
    }
}
