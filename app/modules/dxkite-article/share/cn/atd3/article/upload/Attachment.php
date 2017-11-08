<?php
namespace cn\atd3\article\upload;
use cn\atd3\upload\File;


class Attachment
{
    protected $name;
    protected $source;
    protected $visibility;
    protected $password;
    
    public function __construct(string $source){
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

    public function saveTo(Article $article) {
        proxy('upload')->save();
    }
}
