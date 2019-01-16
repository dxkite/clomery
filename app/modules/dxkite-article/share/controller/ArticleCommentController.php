<?php
namespace dxkite\article\controller;

use dxkite\article\table\ArticleTable;
use dxkite\comment\controller\CommentController;

/**
 * 文章分类
 */
class ArticleCommentController extends CommentController
{
    public function __construct(string $prefix) {
        parent::__construct(ArticleTable::class.'('.$prefix.')');
    }
}
