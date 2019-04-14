<?php
namespace support\setting\controller;

use suda\orm\TableStruct;
use suda\framework\Request;
use support\setting\table\HistoryTable;

class HistoryController
{
    /**
     * 授权表
     *
     * @var HistoryTable
     */
    protected $table;

 
    public function __construct()
    {
        $this->table = new HistoryTable;
    }

    /**
     * 记录访问日志
     *
     * @param string $session
     * @param \suda\framework\Request $request
     * @param string $userId
     * @return void
     */
    public function log(string $session, Request $request , string $userId = null)
    {
        $hash = md5($request->getUrl());
        if ($data = $this->table->read('hash')->where(['session' => $session])->orderBy('time', 'DESC')->one()) {
            if ($data['hash'] == $hash) {
                return true;
            }
        }
        return $this->table->write([
            'ip' => $request->getRemoteAddr(),
            'hash' => $hash ,
            'user' => $userId,
            'session' => $session,
            'time' => time(),
            'url' => $request->getUrl(),
        ])->ok();
    }

    /**
     * 获取最后的
     *
     * @param string $userId
     * @param integer $number
     * @return string
     */
    public function last(string $session, int $number, string $default): string
    {
        if ($data = $this->table->read('url')->where(['session' => $session])->orderBy('time', 'DESC')->limit($number, 1)->one()) {
            return $data['url'];
        }
        return $default;
    }
}
