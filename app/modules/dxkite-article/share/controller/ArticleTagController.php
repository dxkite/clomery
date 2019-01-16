<?php
namespace dxkite\article\controller;


use dxkite\article\table\ArticleTable;
use dxkite\tags\controller\TagController;

/**
 * 文章标签
 */
class ArticleTagController extends TagController
{
    public function __construct(string $prefix) {
        parent::__construct(ArticleTable::class.'('.$prefix.')');
    }
}
