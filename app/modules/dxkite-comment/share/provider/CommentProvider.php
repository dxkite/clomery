<?php
namespace dxkite\comment\provider;

use dxkite\comment\controller\CommentController;

/**
 * 评论
 */
class CommentProvider
{
    protected $name = '';
    protected $commentTable;
    protected $subCommentTable;

    public function __construct(string $name = null)
    {
        $this->controller =  new CommentController($name);
        
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
        return $this->controller->add($target, $content);
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
        return $this->controller->comment($comment, $content);
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
        return $this->controller->reply($subcommet, $content);
    }

    /**
     * 获取目标评论
     *
     * @param integer $target
     * @param integer|null $page
     * @param integer $row
     * @return array|null
     */
    public function getComment(int $target, ?int $page=null, int $row=10):?array
    {
        $comment = $this->controller->getComment($target, $page, $row);
        $rows = $comment['rows'];
        $ids = [];
        foreach ($rows as $index => $row) {
            $ids[] = $row['user'];
            unset($row['ip']);
            $rows[$index] = $row;
        }
        $userInfos = get_user_public_info_array($ids);
        foreach ($rows as $index => $row) {
           $rows[$index]['user'] = $userInfos[$row['user']] ?? $row['user'];
        }
        $comment['rows'] = $rows;
        return $comment;
    }

    /**
     * 获取评论的评论
     *
     * @param integer $comment
     * @param integer|null $page
     * @param integer $row
     * @return array|null
     */
    public function getSubComment(int $comment, ?int $page=null, int $row=10):?array
    {
        $comment = $this->controller->getSubComment($comment, $page, $row);
        $rows = $comment['rows'];
        $ids = [];
        foreach ($rows as $index => $row) {
            $ids[] = $row['user'];
            unset($row['ip']);
            $rows[$index] = $row;
        }
        $userInfos = get_user_public_info_array($ids);
        foreach ($rows as $index => $row) {
            $rows[$index]['user'] = $userInfos[$row['user']] ?? $row['user'];
            $rows[$index]['reply'] = $userInfos[$row['reply']] ?? $row['reply'];
         }
        $comment['rows'] = $rows;
        return $comment;
    }

    /**
     * 删除特定评论
     *
     * @param integer $comment
     * @param boolean $sub
     * @return boolean
     */
    public function delete(int $comment, bool $sub = false):bool
    {
        return $this->controller->delete($comment, $sub);
    }
}
