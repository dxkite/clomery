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
    public static function updateExistHash(string $hash, string $content, int $time)
    {
        $q='UPDATE `#{articles}`  SET `contents` = :content ,  `modified`=:modified WHERE `hash` = :hash LIMIT 1;';
        return (new Query($q, ['hash'=>$hash, 'content'=>$content, 'modified'=>$time]))->exec();
    }

    // 存在文章更新内容
    public static function updateExistId(int $aid, int $author, string $title, string $remark, string $contents, int $time, int $keeptop=0, int $allowreply=1, int $public=1, string $hash='0')
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
            'modified'=>$time,
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
        $q='SELECT `aid`,`title`,`author` as `uid`,`#{users}`.`uname` as `author` ,`remark`,`views`,`modified`,`replys`,`#{category}`.`cid`,`#{category}`.`name` as `category`,`#{category}`.`icon`  FROM `#{articles}` LEFT JOIN  `#{category}` ON `#{category}`.`cid`=`category` LEFT JOIN `#{users}` ON `#{users}`.`uid`=`#{articles}`.`author` WHERE `public`=1 AND `#{articles}`.`verify`=1 AND `#{articles}`.`topic`=:topic ORDER BY `#{articles}`.`modified` DESC LIMIT  :offset,:count;';
        $db=($qs=new Query($q, ['topic'=>$topic, 'count'=>$count, 'offset'=>$offset]))->fetchAll();
        return $db;
    }
    // admin
    public static function listArticles(int $page=1, int $page_count=10)
    {
        $q='SELECT `aid`,`title`,`author` as `uid`,`#{users}`.`uname` as `author` ,`views`,`created`,`modified`,`replys`,`public`,`#{articles}`.`verify`,`#{category}`.`cid`,`#{category}`.`name` as `category` FROM `#{articles}` LEFT JOIN  `#{category}` ON `#{category}`.`cid`=`category` LEFT JOIN `#{users}` ON `#{users}`.`uid`=`#{articles}`.`author` ORDER BY `#{articles}`.`modified` DESC LIMIT  :offset,:count;';
        return (new Query($q, ['offset'=>($page-1)* $page_count>0?($page-1)* $page_count:0, 'count'=>$page_count]))->fetchAll();
    }
    public static function count():int
    {
        $q='SELECT count(`aid`) as `size` FROM `#{articles}` ';
        if ($a=($d=new Query($q))->fetch()) {
            return $a['size'];
        }
        return 0;
    }
    
    public static function getArticlesListByCategory(int $topic, int $categoryid, int $count=10, int $offset=0)
    {
        $q='SELECT `aid`,`title`,`author` as `uid`,`#{users}`.`uname` as `author` ,`remark`,`views`,`modified`,`replys`,`#{category}`.`cid`,`#{category}`.`name` as `category`,`#{category}`.`icon`  FROM `#{articles}` LEFT JOIN  `#{category}` ON `#{category}`.`cid`=`category` LEFT JOIN `#{users}` ON `#{users}`.`uid`=`#{articles}`.`author` WHERE  `public`=1 AND `#{articles}`.`verify`=1 AND `#{articles}`.`topic`=:topic AND `category`=:category ORDER BY `#{articles}`.`modified` DESC LIMIT  :offset,:count;';
        $db=($qs=new Query($q, ['topic'=>$topic, 'category'=>$categoryid, 'count'=>$count, 'offset'=>$offset]))->fetchAll();
        // var_dump($qs->error());
        return $db;
    }
    public static function getArticlesListByTag(int $topic, int $tid, int $count=10, int $offset=0)
    {
        $q='SELECT
  `#{articles}`.`aid`,
  `title`,
  `author` AS `uid`,
  `#{users}`.`uname` AS `author`,
  `remark`,
  `views`,
  `modified`,
  `replys`,
  `#{category}`.`cid`,
  `#{category}`.`name` AS `category`,
  `#{category}`.`icon`
FROM
  `#{article_tag}`
JOIN
  `#{articles}` ON `#{articles}`.`aid` = `#{article_tag}`.`aid` AND `#{articles}`.`topic` = :topic
LEFT JOIN
  `#{category}` ON `#{category}`.`cid` = `#{articles}`.`category`
LEFT JOIN
  `#{users}` ON `#{users}`.`uid` = `#{articles}`.`author`
WHERE  `public`=1 AND `#{articles}`.`verify`=1 AND 
  `#{article_tag}`.`tid` = :tid ORDER BY `#{articles}`.`modified` DESC LIMIT  :offset,:count;';

        $db=($qs=new Query($q, ['topic'=>$topic, 'tid'=>$tid, 'count'=>$count, 'offset'=>$offset]))->fetchAll();
        // var_dump($qs->error());
        return $db;
    }
    public static function getArticleInfo(int $aid)
    {
        $q='SELECT `aid`,`title`,`author` as `uid`,`#{users}`.`uname` as `author` ,`remark`,`views`,`modified`,`replys`,`public`,`#{articles}`.`verify`,`#{category}`.`cid`,`#{category}`.`name` as `category`,`#{category}`.`icon` FROM `#{articles}` LEFT JOIN  `#{category}` ON `#{category}`.`cid`=`category` LEFT JOIN `#{users}` ON `#{users}`.`uid`=`#{articles}`.`author` WHERE `aid`=:aid LIMIT 1;';
        return ($qs=new Query($q, ['aid'=>$aid]))->fetch();
    }
    // TODO: 更多编辑项
    public static function getArticle(int $aid)
    {
        $q='SELECT `aid`,`title`,`remark`,`contents` FROM `#{articles}` WHERE `aid`=:aid LIMIT 1;';
        return (new Query($q, ['aid'=>$aid]))->fetch();
    }
    public static function setArticle(int $aid, string $title, string $remark, string $contents)
    {
        $c='UPDATE `#{articles}` SET `title` = :title,`remark`=:remark,`contents`=:contents WHERE `aid` = :aid LIMIT 1;';
        return (new Query($c, ['aid'=>$aid, 'title'=>$title, 'remark'=>$remark, 'contents'=>$contents]))->exec();
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

    public static function countPublic():int
    {
        $q='SELECT count(`aid`) as `size` FROM `#{articles}` WHERE `public`=1 AND `#{articles}`.`verify`=1 ;';
        if ($a=($d=new Query($q))->fetch()) {
            return $a['size'];
        }
        return 0;
    }
    
    public static function setCategory($aid, $categoryid)
    {
        return Blog_Category::setCategory($aid, $categoryid);
    }

    public static function setTopic($aid, $topic)
    {
        $q='UPDATE `#{articles}` SET `topic` = :topic WHERE `#{articles}`.`aid` = :aid;';
        return (new Query($q, ['aid'=>$aid, 'topic'=>$topic]))->exec();
    }
    // 数据不对时分析
    public static function analyzeArticle(string $table)
    {
        return (new Query('ANALYZE TABLE `#{articles}`'))->exec();
    }
    public function delete(int $aid)
    {
        return (new Query('DELETE FROM `#{articles}` WHERE `aid`=:aid', ['aid'=>$aid]))->exec();
    }
    public function publish(int $aid,int $public=1)
    {
         $q='UPDATE `#{articles}` SET `public` = :public WHERE `#{articles}`.`aid` = :aid;';
        return (new Query($q, ['aid'=>$aid ,'public'=>$public]))->exec();
    }

    public function verify(int $aid,int $verify=1)
    {
         $q='UPDATE `#{articles}` SET `verify` = :verify WHERE `#{articles}`.`aid` = :aid;';
        return (new Query($q, ['aid'=>$aid ,'verify'=>$verify]))->exec();
    }
}
