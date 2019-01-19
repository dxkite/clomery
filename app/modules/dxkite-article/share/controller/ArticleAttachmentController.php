<?php
namespace dxkite\article\controller;

use dxkite\support\file\File;
use dxkite\support\file\Media;
use dxkite\support\view\PageData;
use dxkite\support\file\UploadFile;
use dxkite\support\view\TablePager;
use dxkite\article\table\AttachmentTable;
use dxkite\support\table\file\FileInfoTable;

/**
 * 文章附件
 */
class ArticleAttachmentController
{
    /**
     * 文章附件
     *
     * @var AttachmentTable
     */
    protected $table;

    public function __construct(string $prefix='')
    {
        $this->table = new AttachmentTable($prefix);
    }

    /**
     * 添加附件资源
     *
     * @param integer $article
     * @param string $name
     * @param File $attachment
     * @return UploadFile|null
     */
    public function addAttachment(int $article, string $name, File $attachment):?UploadFile
    {
        $file=Media::saveFile($attachment);
        if ($file !== null) {
            if ($this->addResource($article, $name, $file->getId(), AttachmentTable::TYPE_ATTACHMEMT)){
                return $file;
            }
        }
        return null;
    }
    /**
     * 添加图片资源
     *
     * @param integer $article
     * @param string $name
     * @param File $image
     * @return UploadFile|null
     */
    public function addImage(int $article, string $name, File $image):?UploadFile
    {
        $file=Media::saveFile($image);
        if ($file !== null) {
            if ($this->addResource($article, $name, $file->getId(), AttachmentTable::TYPE_RESOURCE)){
                return $file;
            }
        }
        return null;
    }

    /**
     * 添加资源
     *
     * @param integer $article
     * @param string $name
     * @param integer $resource
     * @param integer $type
     * @return boolean
     */
    public function addResource(int $article, string $name, int $resource, int $type): bool
    {
        if ($this->table->insert([
                'aid'=>$article,
                'name'=>$name,
                'fid'=>$resource,
                'time'=>time(),
                'ip'=>request()->ip(),
                'type'=> $type
            ]) > 0) {
            return true;
        }
        return false;
    }

    
    /**
     * 添加附件
     *
     * @param integer $articleId
     * @param File $file
     * @param string $name
     * @param integer $visibility
     * @param string|null $password
     * @return integer|null
     */
    public function add(int $articleId, File $file, string $name, int $visibility, ?string $password=null):?int
    {
        switch ($visibility) {
            case UploadFile::FILE_PUBLIC:$upload=Media::saveFile($file);break;
            case UploadFile::FILE_SIGN:$upload=Media::saveFileOnline($file);break;
            case UploadFile::FILE_PASSWORD:$upload=Media::saveFileProtected($file, $password);break;
            case UploadFile::FILE_PROTECTED:$upload=Media::saveFilePrivate($file);break;
            default:return false;
        }
        if ($upload && $upload->getId() > 0) {
            if (empty($name)) {
                $name=$file->getName();
            }
            return $this->addResource($articleId, $name, $upload->getId(), AttachmentTable::TYPE_ATTACHMEMT);
        }
        return null;
    }
    
    /**
     * 获取附件列表
     *
     * @param integer $article
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getAttachmentList(int $article, ?int $page=1, int $count=10):PageData
    {
        return TablePager::listWhere($this->table->setFields(['id','name','fid']), ['aid' => $articleId,'type'=>AttachmentTable::TYPE_ATTACHMEMT], [], $page, $count);
    }
 
    /**
     * 获取附件列表
     *
     * @param integer $articleId
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getList(int $articleId, ?int $page=1, int $count=10):PageData
    {
        $pageData = $this->getAttachmentList($articleId, $page, $count);
        $rows = $pageData->getRows();
        
        $id2name =[];
        $fid2attid=[];
        $attachmentIds=[];

        if ($rows !== null) {
            foreach ($rows as $item) {
                $attachmentIds[] = $item['fid'];
                $fid2attid[$item['fid']] = $item['id'];
                $id2name[$item['fid']] = $item['name'];
            }
        }
        $fileTable = new FileInfoTable;
        $data = $fileTable -> select(['id','name','type','user','size','time','visibility'], ['id'=>$attachmentIds])->fetchAll();
        if (\is_array($data)) {
            foreach ($data as $i=>$daItem) {
                $data[$i]['attachment']['name'] = $id2name[$daItem['id']];
                $data[$i]['attachment']['id'] = $fid2attid[$daItem['id']];
            }
        }
        $pageData->setRows($data);
        return $pageData;
    }

    /**
     * 编辑附件属性
     *
     * @param integer $id
     * @param string $name
     * @param integer $visibility
     * @param string|null $password
     * @return integer
     */
    public function edit(int $id, string $name, int $visibility, ?string $password=null):int
    {
        $att = $this->table->select(['fid'], ['id'=>$id])->fetch();
        if ($att !== null&& $visibility >=0 && $visibility<4) {
            $this->table->update(['name'=>$name], ['id'=>$id]);
            $fileTable = new FileInfoTable;
            return $fileTable->updateByPrimaryKey($att['fid'], [
                'visibility'=>$visibility,
                'password'=>$password
            ]);
        }
        return null;
    }

    public function delete(int $id, int $fileId=0)
    {
        if ($fileId) {
            return $this->deleteByPrimaryKey($id) && Media::delete($fileId);
        } elseif ($data=table('attachment')->select(['id','fid'], ['id'=>$id])->fetch()) {
            return $this->deleteByPrimaryKey($id) && Media::delete($data['fid']);
        }
        return false;
    }
}
