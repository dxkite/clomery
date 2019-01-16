<?php
namespace dxkite\article\controller;


use suda\exception\SQLException;
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

    /**
     * 给文章添加标签
     *
     * @param integer $article
     * @param array $tags
     * @return boolean
     */
    public function addTags(int $article,array $tags):bool {
        try {
            $this->tagsTable->begin();
            $tagids = [];
            $user = \get_user_id();
            foreach ($tags as $tagName) {
                $tagids[] =$this->add($user,$tagName);
            }
            $this->unbindAllTags($article);
            $this->bindTags($article,$tagids);
            $this->tagsTable->commit();
        } catch (SQLException $e) {
            $this->tagsTable->rollBack();
            return false;
        }
        return true;
    }

    /**
     * 根据文章获取标签
     *
     * @param integer $article
     * @return array|null
     */
    public function getArticleTags(int $article):?array {
        $tagsId = $this->getTagByRef($article);
        if(\is_array($tagsId)) {
            $ids = [];
            foreach ($tagsId as  $value) {
                $ids[] = $value['tag'];
            }
            return $this->getTagByIds($ids);
        }
        return null;
    }
}
