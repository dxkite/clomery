<?php
namespace cn\atd3\article\upload;

use cn\atd3\upload\File;
use cn\atd3\article\dao\ArticleDAO;
use cn\atd3\article\upload\exception\ResourceException;
use suda\core\Query;

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
    public function __construct(File $archive, string $type)
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
    public function save(int $uid, int $status) : bool
    {
        $article=$this->article;

        $result=false;
        try {
            Query::begin();
           
            // 设置了ID则修改文章
            if ($article->attr['id']??0) {
                $articleId=$article->attr['id'];
                $attr=array_merge($article->attr, [
                    'status'=>$article->attr['status']??$status,
                    'content'=>$article->content
                ]);
                $result=proxy('article')->edit($articleId, static::valInSet(['title','slug','category','abstract','create','modify','status'], $attr));
            } else {
                // 创建新的文章
                $articleId=proxy('article')->create(
                    $article->attr['title'],
                 
                    $article->content,
                    intval($article->attr['category']??0),
                    ArticleDAO::TYPE_HTML,
                    $status
                );
                $article->attr['id'] = $articleId;
                proxy('article')->setAbstract($articleId, $article->attr['abstract']);
                $result=$articleId>0;
            }
            if ($result) {
                if ($article->contentType == 'html') {
                    $content=$article->content;
                    $content=static::contentHTMLParser($this->archive, $articleId, $content);
                    // 修改文章内容
                    $contentOk = proxy('article')->edit($articleId, ['content'=>$content]);
                }
                // 获取附件列表
                $attachments=$article->attachment;
                if (is_array($attachments)) {
                    foreach ($attachments as $attachment) {
                        // 保存附件
                        $attachment->saveTo($article);
                    }
                }
            }
            Query::commit();
        } catch (ResourceException $e) {
            Query::rollBack();
            throw $e;
        }
        return $result;
    }

    public static function contentHTMLParser($archive, int $id, string $html)
    {
        $html=static::purify($html);
        return preg_replace_callback('/<img\s+src="(.+?)"\s+alt="(.+?)"\s+\/>/', function ($match) use ($archive, $id) {
            if (!preg_match('/^https?/', $match[1])) {
                $path=$archive->getRootPath().'/'.$match[1];
                if (storage()->exist($path)) {
                    $id=table('attachment')->addArticleImage(new File($path), $id);
                    if ($id>0) {
                        return '<img src="[[data:'.$id.']]" alt="'.$match[2].'"/>';
                    } else {
                        throw new ResourceException('image resource not prepared: '.$match[1]);
                    }
                } else {
                    throw new ResourceException('image resource not prepared: '.$match[1]);
                }
            }
            return $match[0];
        }, $html);
    }
    
    public static function purify(string $html)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', storage()->path(CACHE_DIR.'/html_purifier'));
        $config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional'); // replace with your doctype
        $purifier = new \HTMLPurifier($config);
        $pure_html = $purifier->purify($html);
        return $pure_html;
    }

    public static function valInSet(array $vals, array $source)
    {
        $attr=[];
        foreach ($vals as $name) {
            if (isset($source[$name])) {
                $attr[$name]=$source[$name];
            }
        }
        return $attr;
    }

    public function __destruct()
    {
        $this->archive->remove();
    }
}
