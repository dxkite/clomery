<?php
/**
 * Created by IntelliJ IDEA.
 * User: dxkite
 * Date: 2019/4/30 0030
 * Time: 8:54
 */

namespace clomery\article\provider;

use dxkite\openuser\provider\VisitorAwareProvider;
use support\openmethod\exception\PermissionException;
use support\openmethod\parameter\File;
use support\upload\provider\BlockUploadProvider;

/**
 * 文件上传工具
 *
 * Class UploadProvider
 * @package clomery\article\provider
 */
class UploadProvider extends VisitorAwareProvider
{

    /**
     * 小文件直接上传
     *
     * @param File $file
     * @return array|null
     */
    public function upload(File $file) {
        $data = $this->getUploader()->upload($file);
        if ($data !== null) {
            $data['uri'] = $this->application->getUribase($this->request).$data['path'];
        }
        return $data;
    }

    /**
     * 获取上传工具
     * @return BlockUploadProvider
     */
    protected function getUploader() {
        // 只允许登陆用户上传
        if ($this->visitor->isGuest()) {
            throw new PermissionException([]);
        }
        $block = new BlockUploadProvider();
        $block->loadFromContext($this->context);
        return $block;
    }
}