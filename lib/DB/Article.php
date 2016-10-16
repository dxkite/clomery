<?php
class DB_Article
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
        $q='SELECT `aid`,`title`,`author` as `uid`,`atd_users`.`uname` as `author` ,`remark`,`views`,`modified`,`replys`,`atd_category`.`cid`,`atd_category`.`name` as `category`,`atd_category`.`icon`  FROM `atd_articles` LEFT JOIN  `atd_category` ON `atd_category`.`cid`=`category` LEFT JOIN `atd_users` ON `atd_users`.`uid`=`atd_articles`.`author` WHERE `topic`=:topic ORDER BY `atd_articles`.`modified` DESC LIMIT  :offset,:count;';
        $db=($qs=new Query($q, ['topic'=>$topic, 'count'=>$count, 'offset'=>$offset]))->fetchAll();
        return $db;
    }
    public static function getArticleInfo(int $aid)
    {
        $q='SELECT `aid`,`title`,`author` as `uid`,`atd_users`.`uname` as `author` ,`remark`,`views`,`modified`,`replys`,`atd_category`.`cid`,`atd_category`.`name` as `category`,`atd_category`.`icon` FROM `atd_articles` LEFT JOIN  `atd_category` ON `atd_category`.`cid`=`category` LEFT JOIN `atd_users` ON `atd_users`.`uid`=`atd_articles`.`author` WHERE `aid`=:aid LIMIT 1;';
        return ($qs=new Query($q, ['aid'=>$aid]))->fetch();
    }

    public static function getArticleContent(int $aid)
    {
        $q='SELECT `contents` FROM `#{articles}` WHERE `aid`=:aid LIMIT 1;';
        $c='UPDATE `#{articles}` SET `views` = `views` +1 WHERE `aid` = :aid LIMIT 1;';
        if ($content=(new Query($q, ['aid'=>$aid]))->fetch()) {
            // 添加Views
            (new Query($c, ['aid'=>$aid]))->exec();
            return $content['contents'];
        }
        return '';
    }
    public static function numbers():int
    {
        $q='SELECT `TABLE_ROWS` as `size` FROM `information_schema`.`TABLES` WHERE  `TABLE_SCHEMA`="'.conf('Database.dbname').'" AND `TABLE_NAME` ="#{articles}" LIMIT 1;';
        if ($a=($d=new Query($q))->fetch()) {
            return $a['size'];
        }
        return 0;
    }
    
    public static function setCategory($aid, $categoryid)
    {
        $q='UPDATE `atd_articles` SET `category` = :cid WHERE `atd_articles`.`aid` = :aid;';
        return (new Query($q, ['aid'=>$aid, 'cid'=>$categoryid]))->exec();
    }

    public static function setTopic($aid, $topic)
    {
        $q='UPDATE `atd_articles` SET `topic` = :topic WHERE `atd_articles`.`aid` = :aid;';
        return (new Query($q, ['aid'=>$aid, 'topic'=>$topic]))->exec();
    }
    // 数据不对时分析
    public static function analyzeArticle(string $table){
        return (new Query('ANALYZE TABLE `#{articles}`'))->exec();    
    }
}
