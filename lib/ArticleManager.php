<?php
class ArticleManager
{
    public static function InsertNew(int $author, string $title, string $remark, string $contents, int $time, int $keeptop=0, int $allowreply=1, int $public=1, string $hash='0')
    {
        $q='INSERT INTO `#{articles}` (`title`, `remark`, `contents`, `author`, `created`, `modified`, `keep_top`, `public`, `allow_reply`, `hash`) VALUES
                                      (:title ,:remark,:contents,:author, :created,:modified ,:top , :public , :allow_reply , :hash );';
        if (($qy=new Query($q, [
            'author'=>$author,
            'title'=>$title,
            'remark'=>$remark,
            'contents'=>$contents,
            'created'=>$time,
            'modified'=>$time,
            'top'=>$keeptop,
            'allow_reply'=>$allowreply,
            'public'=>$public,
            'hash'=>$hash,
            ]))->exec()) {
            return $qy->lastInsertId();
        }
        return -$qy->erron();
    }
    public static function getArticlesList(int $topic=0, int $count=10, int $offset=0)
    {
        $q='SELECT * FROM `atd_articles` WHERE `topic`=:topic ORDER BY `atd_articles`.`modified` ASC LIMIT  :offset,:count;';
        $db=($qs=new Query($q, ['topic'=>$topic, 'count'=>$count, 'offset'=>$offset]))->fetchAll();
        return $db;
    }
    
    public static function numbers():int
    {
        $q='SELECT `TABLE_ROWS` as `size` FROM `information_schema`.`TABLES` WHERE  `TABLE_SCHEMA`="'.conf('Database.dbname').'" AND `TABLE_NAME` ="#{articles}" LIMIT 1;';
        if ($a=($d=new Query($q))->fetch()) {
            return $a['size'];
        }
        return 0;
    }
}
