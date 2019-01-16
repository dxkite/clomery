<?php
namespace dxkite\article\controller;

use dxkite\article\table\ArticleTable;
use dxkite\category\controller\CategoryController;

/**
 * 文章分类
 */
class ArticleCategoryController extends CategoryController
{
    public function __construct(string $prefix) {
        parent::__construct(ArticleTable::class.'('.$prefix.')');
    }
}
