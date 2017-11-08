<?php
namespace cn\atd3\article\upload;

class Article
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
            $attachment=new Attachment($obj['src']);
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
}
