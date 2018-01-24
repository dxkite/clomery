<?php
namespace cn\atd3\article\upload\archive;

use cn\atd3\article\upload\Article;
use cn\atd3\article\upload\Attachment;
use cn\atd3\upload\File;
use suda\tool\ZipHelper;
use cn\atd3\article\upload\exception\ResourceException;

/**
 * xml格式压缩文档解析器
 */
class XmlArchive extends Archive
{
    protected $xmlFile;

    public function __construct(File $file)
    {
        parent::__construct($file);
        $this->xmlFile=$this->templatePath.'/index.xml';
    }

    public function toArticle():Article
    {
        $article=new Article;
        libxml_use_internal_errors(true);
        $indexXml=simplexml_load_file($this->xmlFile);
        if ($indexXml) {
            foreach ($indexXml->children() as $child) {
                if ($child->getName()=='attrs') {
                    $article->attr=$this->getXmlAttrValue($child, 'attr', 'attrs')[1];
                } elseif ($child->getName()=='content') {
                    if (isset($child['type'])) {
                        $article->contentType=(string)$child['type'];
                    }
                    $article->content=base64_decode((string)$child);
                } elseif ($child->getName()=='attachments') {
                    foreach ($child->children() as $xchild) {
                        if ($item=$this->getXmlAttachment($xchild)) {
                            $article->attachment[]=$item;
                        }
                    }
                }
            }
            return  $article;
        }
        throw new ResourceException(__('article xml parser error(%d) %s',xml_get_error_code(), xml_error_string()));
    }

    protected function getXmlAttrValue(\SimpleXMLElement $obj, string $childName='attr', string $tagName='attars')
    {
        if ($obj->getName()==$tagName) {
            $name=isset($obj['name'])?(string)$obj['name']:$obj->getName();
            if (isset($obj['value'])) {
                $value=(string)$obj['value'];
              //  $value=base64_decode($value);
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
                    // TODO: add this
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
            $attachment=new Attachment($this->getRootPath().'/'.$obj['src']);
            if (isset($obj['name'])) {
                $attachment->setName($obj['name']);
            }
            if (isset($obj['visibility'])) {
                $attachment->setVisibility($obj['visibility']);
            }
            if (isset($obj['password'])) {
                $attachment->setPassword($obj['password']);
            }
            return $attachment;
        }
        return false;
    }
    

    public function __destruct()
    {
        parent::remove();
    }
}
