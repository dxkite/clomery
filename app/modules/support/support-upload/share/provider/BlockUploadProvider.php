<?php
namespace support\upload\provider;

use support\upload\BlockFile;
use support\upload\BlockFileWriter;
use support\setting\provider\UserSessionAwareProvider;

class BlockUploadProvider extends UserSessionAwareProvider
{
    public function create(string $name, string $md5):?array
    {
        if ($this->context->getVisitor()->getId()) {

        }
        return BlockFileWriter::create($name, $md5, $this->context->getVisitor()->getId(), $this->request->getRemoteAddr());
    }

    public function upload(BlockFile $file):?array
    {
        return BlockFileWriter::upload($this->application->getDataPath().'/upload-temp', $file);
    }

    public function finish(string $id):bool
    {
        $tmpPath = $this->application->getDataPath().'/upload-temp';
        $data = BlockFileWriter::getSaveDataInfo($id);
        $savePath = $this->application->getDataPath().'/upload/'.$data['type'].'/'.$data['hash'].'.'.$data['type'];
        return BlockFileWriter::finish($tmpPath, $id, $savePath);
    }

    public function cancel(string $id)
    {
        $tmpPath = $this->application->getDataPath().'/upload-temp';
        $data = BlockFileWriter::getSaveDataInfo($id);
        if ($data['user'] == $this->context->getVisitor()->getId()) {
            return BlockFileWriter::cancel($tmpPath, $id);
        }
        return false;
    }
}
