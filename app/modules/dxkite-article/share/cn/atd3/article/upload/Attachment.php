<?php
namespace cn\atd3\article\upload;

use cn\atd3\upload\File;
use cn\atd3\upload\UploadProxy;
use cn\atd3\article\upload\exception\ResourceException;

class Attachment
{
    protected $name;
    protected $source;
    protected $visibility;
    protected $password;
    
    public function __construct(string $source)
    {
        $this->setSource($source);
    }

    public function setName(string $name)
    {
        $this->name=$name;
        return $this;
    }
    
    public function setSource(string $source)
    {
        $this->source=$source;
        return $this;
    }
    
    public function setVisibility(string $visibility)
    {
        $this->visibility=$visibility;
        return $this;
    }
    
    public function setPassword(string $password)
    {
        $this->password=$password;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function getVisibility()
    {
        return $this->visibility;
    }
    
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * 将附件保存至文章
     *
     * @param Article $article
     * @return bool 是否保存成功
     */
    public function saveTo(Article $article):bool
    {
        $articleId=$article->attr['id'];
        
        if (strtolower($this->getVisibility()) == 'public') {
            $statu=UploadProxy::FILE_PUBLIC;
        } elseif (strtolower($this->getVisibility()) == 'password') {
            $statu=UploadProxy::FILE_PASSWORD;
        } else {
            $statu=UploadProxy::FILE_SIGN;
        }
        if (storage()->exist($this->source)) {
            $file = new File($this->source);

            if ($statu == UploadProxy::FILE_PASSWORD) {
                $upload=proxy('upload')->save($file, 'article_resource_'.$articleId, UploadProxy::STATE_PUBLISH, $statu, $this->getPassword());
            } else {
                $upload=proxy('upload')->save($file, 'article_resource_'.$articleId, UploadProxy::STATE_PUBLISH, $statu);
            }
           
            if ($upload->getId() > 0) {
                $insertId= table('attachment')->addArticleResource($articleId, $this->name, $upload->getId());
            } else {
                throw new ResourceException('resource not prepared: '.$this->source);
            }
            return $insertId>0;
        } else {
            throw new ResourceException('resource not exist: '.$this->source);
        }
        return false;
    }
}
