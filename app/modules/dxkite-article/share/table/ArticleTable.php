<?php
namespace dxkite\article\table;

use dxkite\content\parser\Content;
use dxkite\article\table\PrefixTable;

class ArticleTable extends PrefixTable
{
    const STATUS_DELETE=0;     // 删除
    const STATUS_DRAFT=1;      // 草稿
    const STATUS_PUBLISH=2;    // 发布

    const TYPE_MARKDOWN=0;
    const TYPE_HTML=1;
    const TYPE_PLAIN=2;

    public function __construct(string $prefix='')
    {
        parent::__construct($prefix, 'article');
    }

    public function onBuildCreator($table)
    {
        return $table->fields([
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('user', 'bigint', 20)->unsigned()->key()->comment("作者"),
            $table->field('title', 'varchar', 255)->key()->comment("标题"),
            $table->field('category', 'bigint', 20)->key()->comment("文章分类"),
            $table->field('slug', 'varchar', 255)->key()->comment("缩写"),
            $table->field('cover', 'bigint', 20)->comment("封面文件ID"),
            $table->field('abstract', 'text')->comment("摘要"),
            $table->field('content', 'text')->comment("内容"),
            $table->field('create', 'int', 11)->key()->comment("创建时间"),
            $table->field('modify', 'int', 11)->key()->comment("修改时间"),
            $table->field('ip', 'varchar', 32)->comment("编辑IP"),
            $table->field('views', 'int', 11)->key()->comment("阅读量"),
            $table->field('status', 'tinyint', 1)->key()->comment("状态"),
        ]);
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

    /** abstract */
    /**
     * 将内容解码成HTML格式
     *
     * @param string $abstract
     * @return Content
     */
    protected function _outputAbstractField(string $abstract)
    {
        // 解码成对象
        if ($object = content_unpack($abstract)) {
            return $object;
        }
        // 未设置解码则按markdown编码
        return content_create($abstract, Content::MD);
    }

    /**
     * 使用Markdown 对内容进行默认编码
     *
     * @param string|Content $abstract
     * @return string
     */
    protected function _inputAbstractField($abstract)
    {
        return content_pack($abstract, Content::MD);
    }
}
