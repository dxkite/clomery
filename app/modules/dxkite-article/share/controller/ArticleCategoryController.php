<?php
namespace dxkite\article\controller;

use dxkite\support\util\Pinyin;
use dxkite\article\table\ArticleTable;
use dxkite\category\controller\CategoryController;

/**
 * 文章分类
 */
class ArticleCategoryController extends CategoryController
{
    public function __construct(string $prefix)
    {
        parent::__construct(ArticleTable::class.'('.$prefix.')');
    }

    public function save(string $name,?string $slug =null, int $parent=0) {
        $id = $this->getByName($name);
        if ($id !== null) {
            return $id['id'];
        }
        $slug = $slug ?? Pinyin::getAll($name, '-', 255);
        return $this->add(\get_user_id(), $name, $slug, $parent);
    }
}
