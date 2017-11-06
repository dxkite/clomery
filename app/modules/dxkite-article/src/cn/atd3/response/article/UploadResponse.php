<?php
namespace cn\atd3\response;

use cn\atd3\api\response\OnCallableResponse;
use cn\atd3\user\UserProxy;
use cn\atd3\upload\File;
use suda\tool\ZipHelper;

class ArticleArchive
{
    public $attr;
    public $content;
    public $contentType;
    public $attachment;

    const XML=0;
    const MARKDOWN=1;

    public function __construct(string $path, int $type=0)
    {
        if ($type == self::XML) {
            $this->parseXml($path);
        }
    }

    protected function parseXml(string $xmlFile)
    {
        $indexXml=simplexml_load_file($xmlFile);
        foreach ($indexXml->children() as $child) {
            if ($child->getName()=='attrs') {
                $this->attr=$this->getXmlAttrValue($child, 'attr', 'attrs')[1];
            } elseif ($child->getName()=='content') {
                if (isset($child['type'])) {
                    $this->contentType=(string)$child['type'];
                }
                $this->content=base64_decode((string)$child);
            } elseif ($child->getName()=='attachments') {
                foreach ($child->children() as $xchild) {
                    if ($item=$this->getXmlAttachment($xchild)) {
                        $this->attachment[]=$item;
                    }
                }
            }
        }
    }

    protected function getXmlAttrValue(\SimpleXMLElement $obj, string $childName='attr', string $tagName='attars')
    {
        if ($obj->getName()==$tagName) {
            $name=isset($obj['name'])?(string)$obj['name']:$obj->getName();
            if (isset($obj['value'])) {
                $value=(string)$obj['value'];
                $value=base64_decode($value);
            } else {
                $children=$obj->children();
                if (count($children)) {
                    foreach ($children as $cvalue) {
                        if ($arr=$this->getXmlAttrValue($cvalue, 'value', $childName)) {
                            list($n, $v)=$arr;
                            $value[$n]=$v;
                        }
                    }
                } else {
                    $value=(string)$obj;
                    $value=base64_decode($value);
                }
            }
            return [$name,$value];
        }
        return false;
    }

    protected function getXmlAttachment(\SimpleXMLElement $obj)
    {
        if (isset($obj['src']) && $obj->getName()==='attarchment') {
            $name= (string)$obj['name'];
            $src =(string)$obj['src'];
            $visibility =(string)$obj['visibility'];
            $password =(string)$obj['password'];
            return ['name'=>$name,'src'=>$src,'visibility'=>$visibility,'password'=>$password];
        }
        return false;
    }
}

class UploadResponse extends OnCallableResponse
{
    public function article(/*File $article*/)
    {
        //var_dump($article);
        // $path=TEMP_DIR.'/article_temp';
        // $path=TEMP_DIR.'/article_temp/'.md5($article->getPath());
        // ZipHelper::unzip($article->getPath(),$path);
        $xmlFile='d:\Server\Local\dxsite\app\data\temp\article_temp\index.xml';
        $article=new ArticleArchive($xmlFile);
        return $article;
    }
}
