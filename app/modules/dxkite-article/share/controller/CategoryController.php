<?php
namespace dxkite\article\controller;

use dxkite\article\table\ArticleTable;
use dxkite\category\controller\CategoryController as ModuleCategoryController;

/**
 * 文章分类
 */
class CategoryController extends ModuleCategoryController
{
    public function __construct(string $prefix) {
        parent::__construct(ArticleTable::class.'('.$prefix.')');
    }
}
