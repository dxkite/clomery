<?php
namespace cn\atd3\article\upload;

use cn\atd3\upload\File;

/**
 * 文章归档
 *  
 */
class ArticleArchive
{
   
    protected $archive;

    /**
     * 实例化
     * 
     * @source POST
     * @param File $archive
     */
    public function __construct(File $archive,string $type)
    {
        $className='cn\atd3\article\upload\archive\\'.ucfirst($type).'Archive';
        $this->archive=(new $className($archive))->toArticle();
    }

    /**
     * 保存文章到数据库
     *
     * @param int $status
     * @return bool
     */
    public function save(int $status) : bool
    {
        var_dump($this->archive);
        return true;
    }
}
