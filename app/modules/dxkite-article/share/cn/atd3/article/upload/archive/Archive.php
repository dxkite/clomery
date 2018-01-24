<?php
namespace cn\atd3\article\upload\archive;

use cn\atd3\article\upload\Article;
use cn\atd3\upload\File;
use suda\tool\ZipHelper;

abstract class Archive
{
    protected $templatePath;
    public function __construct(File $file)
    {
        $path=TEMP_DIR.'/upload_temp/'.md5_file($file->getPath());
        ZipHelper::unzip($file->getPath(), $path);
        $this->templatePath=$path;
    }
    public function getRootPath() {
        return  $this->templatePath;
    }
    abstract public function toArticle():Article;

    public function remove()
    {
        return storage()->delete($this->templatePath);
    }
}
