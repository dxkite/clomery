<?php
namespace dxkite\comment\controller;

use dxkite\comment\table\CommentTable;
use dxkite\comment\table\SubCommentTable;
use dxkite\support\view\TablePager;
use suda\tool\Command;

/**
 * 评论
 */
class CommentController
{
    protected $commentTable;
    protected $subCommentTable;
    protected $target;

    public function __construct(string $target = null)
    {
        $this->target = Command::newClassInstance($target);
        $this->commentTable= new CommentTable($this->target);
        $this->subCommentTable= new SubCommentTable($this->target);
    }

    /**
     * 评论
     *
     * @param integer $target
     * @param string $content
     * @return integer
     */
    public function add(int $target, string $content):int
    {
        if ($targetData = $this->target->getByPrimaryKey($target)) {
            $user =  get_user_id();
            return $this->commentTable->insert([
                'user' => $user,
                'target' => $target,
                'content' => $content,
                'time' => time(),
                'ip' => request()->ip(),
                'status' =>  CommentTable::STATUS_NORMAL,
            ]);
        }
        return 0;
    }

    /**
     * 评论评论
     *
     * @param integer $comment
     * @param string $content
     * @return integer
     */
    public function comment(int $comment, string $content):int
    {
        $user =  get_user_id();
        if ($parent = $this->commentTable->getByPrimaryKey($comment)) {
            return $this->subCommentTable->insert([
                'user' => $user,
                'target' => $parent['target'],
                'content' => $content,
                'parent'=> $comment,
                'time' => time(),
                'ip' => request()->ip(),
                'status' =>  CommentTable::STATUS_NORMAL,
            ]);
        }
        return 0;
    }

    /**
     * 删除评论
     *
     * @param integer $comment
     * @param boolean $sub
     * @return boolean
     */
    public function delete(int $comment, bool $sub = false):bool
    {
        $user =  get_user_id();
        $table = $sub ? $this->subCommentTable : $this->commentTable;
        if ($that = $table->getByPrimaryKey($comment)) {
            // 只有自己能够删除
            if ($that['user'] == $user) {
                return $table->updateByPrimaryKey($that['id'], [
                    'status' =>  CommentTable::STATUS_DELETE,
                ]) > 0;
            }
        }
    }

    /**
     * 回复子评论
     *
     * @param integer $subcommet
     * @param string $content
     * @return integer
     */
    public function reply(int $subcommet, string $content):int
    {
        $user =  get_user_id();
        if ($parent = $this->subCommentTable->getByPrimaryKey($subcommet)) {
            return $this->subCommentTable->insert([
                'user' => $user,
                'target' => $parent['target'],
                'reply' => $parent['user'],
                'content' => $content,
                'parent'=> $parent['parent'],
                'time' => time(),
                'ip' => request()->ip(),
                'status' =>  CommentTable::STATUS_NORMAL,
            ]);
        }
        return 0;
    }

    /**
     * 获取目标评论
     *
     * @param integer $target
     * @param integer|null $page
     * @param integer $row
     * @return array|null
     */
    public function getComment(int $target, ?int $page, int $row = 10):?array
    {
        return TablePager::listWhere($this->commentTable, ['target' => $target, 'status' => CommentTable::STATUS_NORMAL], [], $page, $row);
    }

    /**
     * 获取评论的评论
     *
     * @param integer $comment
     * @param integer|null $page
     * @param integer $row
     * @return array|null
     */
    public function getSubComment(int $comment, ?int $page, int $row = 10):?array
    {
        return TablePager::listWhere($this->subCommentTable, ['parent' => $comment, 'status' => CommentTable::STATUS_NORMAL], [], $page, $row);
    }
}
