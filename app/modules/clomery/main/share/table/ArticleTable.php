<?php


namespace clomery\main\table;


use clomery\content\parser\Content;
use clomery\content\table\ContentTable;
use suda\application\database\creator\MySQLTableCreator;
use suda\application\database\Database;
use suda\database\exception\SQLException;

class ArticleTable extends ContentTable
{
    public function __construct()
    {
        parent::__construct('article');
        $cacheKey = 'auto-create-'.$this->getName();
        $cache = Database::application()->cache();
        // 避免多次重复创建表
        if ($cache->has($cacheKey) === false && SUDA_DEBUG) {
            try {
                (new MySQLTableCreator())->create($this->getSource()->write(), $this->getStruct());
                $cache->set($cacheKey, true, 0);
            } catch (SQLException $e) {
                Database::application()->dumpException($e);
            }
        }
    }

    /** content */
    /**
     * 使用Markdown 对内容进行默认编码
     *
     * @param string|Content $content
     * @return string
     */
    protected function _inputContentField($content)
    {
        return content_pack($content, Content::MD);
    }

    /**
     * 将内容解码成HTML格式
     *
     * @param string $content
     * @return Content
     */
    protected function _outputContentField(string $content)
    {
        // 解码成对象
        if ($object = content_unpack($content)) {
            return $object;
        }
        // 未设置解码则按markdown编码
        return content_create($content, Content::MD);
    }

    /** excerpt */
    /**
     * 将内容解码成HTML格式
     *
     * @param string $excerpt
     * @return Content
     */
    protected function _outputDescriptionField(string $excerpt)
    {
        // 解码成对象
        if ($object = content_unpack($excerpt)) {
            return $object;
        }
        // 未设置解码则按markdown编码
        return content_create($excerpt, Content::MD);
    }

    /**
     * 使用Markdown 对内容进行默认编码
     *
     * @param string|Content $excerpt
     * @return string
     */
    protected function _inputDescriptionField($excerpt)
    {
        return content_pack($excerpt, Content::MD);
    }
}