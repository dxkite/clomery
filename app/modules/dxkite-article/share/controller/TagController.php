<?php
namespace dxkite\article\controller;


use dxkite\tags\controller\TagController as ModuleTagController;

/**
 * 文章标签
 */
class TagController extends ModuleTagController
{
    public function __construct(string $prefix) {
        parent::__construct(ArticleTable::class.'('.$prefix.')');
    }
}
