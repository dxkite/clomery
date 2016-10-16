<?php

class DB_Tag
{
    public static function checkTag(string $tagname)
    {
        $check='SELECT `tid` FROM `#{tags}` WHERE `name`= :name LIMIT 1;';
        if ($fetch=(new Query($check, ['name'=>$tagname]))->fetch()) {
            return $fetch['tid'];
        }
        return 0;
    }
    public static function insertTag(int $topic, string  $tag)
    {
        $insert='INSERT INTO `atd_tags` (`topic`, `name`, `count`) VALUES (:topic ,:tagname, 0);';
        if ((new Query($insert, ['topic'=>$topic, 'tagname'=>$tag]))->exec()) {
            return Query::lastInsertId();
        }
        return 0;
    }

    public static function addTagToArticle(int $aid, int $topic, string $tag)
    {
        $add='INSERT INTO `#{article_tag}` (`aid`,`tid`)  SELECT :aid,:tid FROM DUAL WHERE NOT EXISTS
         (SELECT `aid`,`tid` FROM `#{article_tag}` WHERE aid=:aid AND tid=:tid );';
        $update='UPDATE `#{tags}` SET `count` = `count`+ 1  WHERE `#{tags}`.`tid` = :tid ;';
        $tagid=0;
        // 修正Tag
        if ($c=self::checkTag($tag)) {
            $tagid=$c;
        } elseif ($i=self::insertTag($topic, $tag)) {
            $tagid=$i;
        } else {
            return false;
        }
        return (new Query($add, ['aid'=>$aid, 'tid'=>$tagid]))->exec() && (new Query($update, ['tid'=>$tagid]))->exec();
    }
    public static function addTagsToArticle(int $aid, int $topic, array $tag)
    {
        foreach ($tag as $tagname) {
            self::addTagToArticle($aid, $topic, $tagname);
        }
    }
    public static function getTags(int $aid, int $topic=0)
    {
        $q='SELECT `atd_tags`.`tid`, `name` FROM `atd_article_tag` JOIN `atd_tags` ON `atd_tags`.`tid`=`atd_article_tag`.`tid` AND `atd_tags`.`topic`=:topic WHERE `atd_article_tag`.`aid` = :aid ';
        return (new Query($q, ['topic'=>$topic, 'aid'=>$aid]))->fetchAll();
    }
}
