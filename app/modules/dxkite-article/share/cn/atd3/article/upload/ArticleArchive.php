<?php
namespace cn\atd3\article\upload;

use cn\atd3\upload\File;
use cn\atd3\article\dao\ArticleDAO;
/**
 * 文章归档
 *  
 */
class ArticleArchive
{
   
    protected $article;
    protected $archive;

    /**
     * 实例化
     * 
     * @source POST
     * @param File $archive
     */
    public function __construct(File $archive,string $type)
    {
        $className='cn\atd3\article\upload\archive\\'.ucfirst($type).'Archive';
        $this->archive=new $className($archive);
        $this->article=$this->archive->toArticle();
    }

    /**
     * 保存文章到数据库
     *
     * @param int $status
     * @return bool
     */
    public function save(int $uid,int $status) : bool
    {
        $article=$this->article;
        // 修改文章
        if ($archive->attr['id']??0){
            $articleId=$article->attr['id'];
            $article->attr['status']=$article->attr['status']??$status;
            $result=proxy('article')->edit($articleId,static::valIfSet(['title','slug','category','create','modify','status'], $article->attr));
        }else{
            $articleId=proxy('article')->create(
                $article->attr['title'],
                $article->content,
                $article->attr['category']??0,
                ArticleDAO::TYPE_HTML,
                $status
            );
            $result=$articleId>0;
        }
        return $result;
    }
    
    public static function resourceMatch() {

    }

    public static function valIfSet(array $vals,array $source){
        $attr=[];
        foreach ($vals as $name){
            if (isset($source[$name])){
                $attr[$name]=$source[$name];
            }
        }
        return $attr;
    }

    public function __destruct() {
        $this->archive->remove();
    }
}
