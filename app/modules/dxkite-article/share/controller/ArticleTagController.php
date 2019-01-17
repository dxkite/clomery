<?php
namespace dxkite\article\controller;

use suda\exception\SQLException;
use dxkite\support\view\PageData;
use dxkite\support\view\TablePager;
use suda\archive\SQLStatementPrepare;
use dxkite\article\table\ArticleTable;
use dxkite\tags\controller\TagController;
use dxkite\article\controller\ArticleController;

/**
 * 文章标签
 */
class ArticleTagController extends TagController
{
    public function __construct(string $prefix)
    {
        parent::__construct(ArticleTable::class.'('.$prefix.')');
    }

    /**
     * 给文章添加标签
     *
     * @param integer $article
     * @param array $tags
     * @return boolean
     */
    public function addTags(int $article, array $tags):bool
    {
        try {
            $this->tagsTable->begin();
            $tagids = [];
            $user = \get_user_id();
            foreach ($tags as $tagName) {
                $tagids[] =$this->add($user, $tagName);
            }
            $this->unbindAllTags($article);
            $this->bindTags($article, $tagids);
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
    public function getArticleTags(int $article):?array
    {
        $tagsId = $this->getTagByRef($article);
        if (\is_array($tagsId)) {
            $ids = [];
            foreach ($tagsId as  $value) {
                $ids[] = $value['tag'];
            }
            return $this->getTagByIds($ids);
        }
        return null;
    }


    /**
     * 根据文章标签ID获取文章列表
     *
     * @param integer|null $user
     * @param integer $tag
     * @param string $field
     * @param integer $order
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getArticleByTag(?int $user=null, int $tag, string $field = 'modify', int $order = ArticleTable::ORDER_DESC, ?int $page=null, int $count=10):PageData
    {
        list($condition, $parameter) = ArticleController::getUserViewCondition($user, 'a.');
        $tableName = '#{'.$this->tagTable->getTableName().'}';
        if (is_null($page)) {
            $limit = '';
        } else {
            $limit = SQLStatementPrepare::prepareLimit([$page,$count]);
        }
        $wants=[];
        foreach (ArticleController::$showFields as $name) {
            $wants[]= 'a.`'.$name.'` as `'.$name.'`';
        }
        $condition = 'b.tag = :tagid AND '. $condition;
        $parameter['tagid'] = $tag;
        $wants = \implode(',',$wants);
        $order = 'ORDER BY `'. $field.'` '. ($order==ArticleTable::ORDER_ASC?'ASC':'DESC');
        $joinWhere = ' FROM %table%  as a JOIN '.$tableName.' as b ON a.id = b.ref WHERE '. $condition;
        $rows = $this->target->query('SELECT '.$wants.$joinWhere.' '. $order.' '.$limit, $parameter)->fetchAll();
        $rowCount =  $this->target->query('SELECT count(a.id) as count '.$joinWhere, $parameter)->fetch();
        return PageData::build($rows, $rowCount?$rowCount['count']:0, $page, $count);
    }
}
