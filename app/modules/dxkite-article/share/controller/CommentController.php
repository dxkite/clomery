<?php
namespace dxkite\article\controller;


use dxkite\comment\controller\CommentController as ModuleCommentController;

/**
 * 文章分类
 */
class CommentController extends ModuleCommentController
{
    public function __construct(string $prefix) {
        parent::__construct(ArticleTable::class.'('.$prefix.')');
    }
}
