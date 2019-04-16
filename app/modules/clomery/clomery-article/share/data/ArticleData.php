<?php
namespace clomery\article\data;

use clomery\article\Content;
use clomery\article\TableData;

/**
 * 文章数据表
 */
class ArticleData extends TableData
{
    const STATUS_DELETE = 0;     // 删除
    const STATUS_DRAFT = 1;      // 草稿
    const STATUS_PUBLISH = 2;    // 发布

    public function __construct(string $name = null)
    {
        parent::__construct($name ?? 'article');
    }

    public function defineFields():array
    {
        return [
            $this->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $this->field('user', 'bigint', 20)->unsigned()->key()->comment('作者'),
            $this->field('title', 'varchar', 255)->key()->comment('标题'),
            $this->field('category', 'bigint', 20)->key()->comment('文章分类'),
            $this->field('slug', 'varchar', 255)->key()->comment('缩写'),
            $this->field('image', 'varchar', 255)->comment('封面'),
            $this->field('excerpt', 'text')->comment('摘要'),
            $this->field('content', 'text')->comment('内容'),
            $this->field('create', 'int', 11)->key()->comment('创建时间'),
            $this->field('modify', 'int', 11)->key()->comment('修改时间'),
            $this->field('ip', 'varchar', 32)->comment('编辑IP'),
            $this->field('views', 'int', 11)->key()->comment('阅读量'),
            $this->field('status', 'tinyint', 1)->key()->comment('状态'),
        ];
    }

    /**
     * 使用Markdown 对内容进行默认编码
     *
     * @param string|Content $content
     * @return string
     */
    protected function _inputContentField($content)
    {
        if (\is_string($content)) {
            return \serialize(new Content($content));
        }
        return  serialize($content);
    }

    /**
     * 将内容解码
     *
     * @param string $content
     * @return Content
     */
    protected function _outputContentField($content)
    {
        return $content?unserialize($content) : new Content('');
    }

    
    /**
     * 使用Markdown 对内容进行默认编码
     *
     * @param string|Content $excerpt
     * @return string
     */
    protected function _inputExcerptField($excerpt)
    {
        if (\is_string($excerpt)) {
            return \serialize(new Content($excerpt));
        }
        return serialize($excerpt);
    }

    /**
     * 将内容解码
     *
     * @param string $excerpt
     * @return Content
     */
    protected function _outputExcerptField($excerpt)
    {
        return $excerpt? unserialize($excerpt) : new Content('');
    }
}
