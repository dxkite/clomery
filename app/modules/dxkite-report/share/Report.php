<?php
namespace cn\atd3\report;

use cn\atd3\proxy\ProxyObject;
use cn\atd3\upload\Media;
use cn\atd3\upload\File;

class Report extends ProxyObject
{

    public function __construc() {

    }
    /**
     * 上传日志文件
     *
     * @param File $report
     * @return bool 
     */
    public function upload(File $report):bool
    {
        $id=(new Media)->save($report, 'report_log', Media::STATE_PROTECTED, Media::FILE_PROTECTED);
        if ($id) {
            $token=cookie()->get('report_log');
            $reportId=table('report')->token2id($token);
            if ($reportId) {
                return table('report_log')->insert(['file'=>$id,'report'=>$reportId]) > 0;
            }
        }
        return false;
    }

    /**
     * 发送信息
     *
     * @param string $name 称呼
     * @param string $message 信息反馈
     * @param [type] $contact 联系方式
     * @return boolean 是否成功
     */
    public function send(string $name, string $message, $contact):bool
    {
        $reportToken=md5($message.time());
        $data=[
            'name'=>$name,
            'message'=>$message,
            'contact'=>$contact,
            'token'=>$reportToken,
            'ip'=>request()->ip(),
            'time'=>time()
        ];
        if ($id=$this->getContext()->getVisitor()->getId()) {
            $data['user']=$id;
        }
        if (table('report')->insert($data) > 0) {
            cookie()->set('report_log', $reportToken);
            return true;
        }
        return false;
    }
}
