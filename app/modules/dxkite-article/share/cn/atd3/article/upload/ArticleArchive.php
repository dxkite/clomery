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

        // 修改文章
        if ($archive->attr['id']??0) {
            $articleId=$article->attr['id'];
            $attr=array_merge($article->attr, [
                'status'=>$article->attr['status']??$status,
                'content'=>$article->content
            ]);
            $result=proxy('article')->edit($articleId, static::valIfSet(['title','slug','category','create','modify','status'], $attr));
        } else {
            $articleId=proxy('article')->create(
                $article->attr['title'],
                $article->content,
                intval($article->attr['category']??0),
                ArticleDAO::TYPE_HTML,
                $status
            );
            $result=$articleId>0;
        }
        if ($result) {
            if ($article->contentType == 'html') {
                $content=$article->content;
                $content=static::contentHTMLParser($this->archive,$articleId, $content);
                return  proxy('article')->edit($articleId, ['content'=>$content]);
            }
        }
        return $result;
    }

    public static function contentHTMLParser($archive,int $id, string $html)
    {
        $html=static::purify($html);
        return preg_replace_callback('/<img\s+src="(.+?)"\s+alt="(.+?)"\s+\/>/',function($match) use ($archive,$id) {
            if (!preg_match('/^https?/',$match[1])) {
                $path=$archive->getRootPath().'/'.$match[1];
                if (storage()->exist($path)){
                    //var_dump($path);
                    $url=table('attachment')->addArticleResource(new File($path),$id);
                    //var_dump($url);
                    return '<img src="'.$url.'" alt="'.$match[2].'"/>';
                }
            } 
            return $match[0];
        },$html);
    }
    
    public static function purify(string $html)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath',CACHE_DIR.'/html_purifier');
        $config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional'); // replace with your doctype
        $purifier = new \HTMLPurifier($config);
        $pure_html = $purifier->purify($html);
        return $pure_html;
    }

    public static function valIfSet(array $vals, array $source)
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
