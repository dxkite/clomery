<?php
class Blog_Article
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
    // 存在相同ZIP:某些资源上传失败刷新
    public static function updateExistHash(string $hash,string $content,int $time)
    {
        $q='UPDATE `#{articles}`  SET `contents` = :content ,  `modified`=:modified WHERE `hash` = :hash LIMIT 1;';
        return (new Query($q, ['hash'=>$hash,'content'=>$content,'modified'=>$time]))->exec();
    }

    // 存在文章更新内容
    public static function updateExistId(int $aid,int $author, string $title, string $remark, string $contents, int $time,int $keeptop=0, int $allowreply=1, int $public=1, string $hash='0')
    {
        $q='UPDATE `#{articles}`  SET 
        `author`=:author, 
        `title`=:title, 
        `remark`=:remark, 
        `contents` = :contents,
        `modified`=:modified, 
        `keep_top`=:top ,
        `public`=:public, 
        `allow_reply`=:reply, 
        `hash`=:hash
        WHERE `aid` = :aid LIMIT 1;';
        $count=($qq=new Query($q, [
            'aid'=>$aid,
            'author'=>$author,
            'title'=>$title,
            'remark'=>$remark,
            'contents'=>$contents,
            'modified'=>$time ,
            'top'=>$keeptop,
            'reply'=>$allowreply,
            'public'=>$public,
            'hash'=>$hash,
            ]))->exec();
        var_dump($qq->error());
        return $count;
    }

    public static function getArticlesList(int $topic=0, int $count=10, int $offset=0)
    {
        $q='SELECT `aid`,`title`,`author` as `uid`,`atd_users`.`uname` as `author` ,`remark`,`views`,`modified`,`replys`,`atd_category`.`cid`,`atd_category`.`name` as `category`,`atd_category`.`icon`  FROM `atd_articles` LEFT JOIN  `atd_category` ON `atd_category`.`cid`=`category` LEFT JOIN `atd_users` ON `atd_users`.`uid`=`atd_articles`.`author` WHERE `public`=1 AND `atd_articles`.`topic`=:topic ORDER BY `atd_articles`.`modified` DESC LIMIT  :offset,:count;';
        $db=($qs=new Query($q, ['topic'=>$topic, 'count'=>$count, 'offset'=>$offset]))->fetchAll();
        return $db;
    }

    public static function getArticlesListByCategory(int $topic,int $categoryid, int $count=10, int $offset=0)
    {
        $q='SELECT `aid`,`title`,`author` as `uid`,`atd_users`.`uname` as `author` ,`remark`,`views`,`modified`,`replys`,`atd_category`.`cid`,`atd_category`.`name` as `category`,`atd_category`.`icon`  FROM `atd_articles` LEFT JOIN  `atd_category` ON `atd_category`.`cid`=`category` LEFT JOIN `atd_users` ON `atd_users`.`uid`=`atd_articles`.`author` WHERE  `public`=1 AND `atd_articles`.`topic`=:topic AND `category`=:category ORDER BY `atd_articles`.`modified` DESC LIMIT  :offset,:count;';
        $db=($qs=new Query($q, ['topic'=>$topic,'category'=>$categoryid, 'count'=>$count, 'offset'=>$offset]))->fetchAll();
        // var_dump($qs->error());
        return $db;
    }
    public static function getArticlesListByTag(int $topic,int $tid, int $count=10, int $offset=0)
    {
        $q='SELECT
  `atd_articles`.`aid`,
  `title`,
  `author` AS `uid`,
  `atd_users`.`uname` AS `author`,
  `remark`,
  `views`,
  `modified`,
  `replys`,
  `atd_category`.`cid`,
  `atd_category`.`name` AS `category`,
  `atd_category`.`icon`
FROM
  `atd_article_tag`
JOIN
  `atd_articles` ON `atd_articles`.`aid` = `atd_article_tag`.`aid` AND `atd_articles`.`topic` = :topic
LEFT JOIN
  `atd_category` ON `atd_category`.`cid` = `atd_articles`.`category`
LEFT JOIN
  `atd_users` ON `atd_users`.`uid` = `atd_articles`.`author`
WHERE  `public`=1 AND
  `atd_article_tag`.`tid` = :tid ORDER BY `atd_articles`.`modified` DESC LIMIT  :offset,:count;';

        $db=($qs=new Query($q, ['topic'=>$topic,'tid'=>$tid, 'count'=>$count, 'offset'=>$offset]))->fetchAll();
        // var_dump($qs->error());
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
        $q='SELECT count(`aid`) as `size` FROM `#{articles}` WHERE `public`=1 LIMIT 1;';
        if ($a=($d=new Query($q))->fetch()) {
            return $a['size'];
        }
        return 0;
    }
    
    public static function setCategory($aid, $categoryid)
    {
        return Blog_Category::setCategory($aid,$categoryid);
    }

    public static function setTopic($aid, $topic)
    {
        $q='UPDATE `atd_articles` SET `topic` = :topic WHERE `atd_articles`.`aid` = :aid;';
        return (new Query($q, ['aid'=>$aid, 'topic'=>$topic]))->exec();
    }
    // 数据不对时分析
    public static function analyzeArticle(string $table)
    {
        return (new Query('ANALYZE TABLE `#{articles}`'))->exec();
    }
    public function delete(int $aid){
        //  TODO
    }
}
