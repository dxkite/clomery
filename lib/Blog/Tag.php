<?php

class Blog_Tag
{
    public static function getTagId(string $tagname)
    {
        $check='SELECT `tid` FROM `#{tags}` WHERE `name`= :name LIMIT 1;';
        if ($fetch=(new Query($check, ['name'=>$tagname]))->fetch()) {
            return $fetch['tid'];
        }
        return 0;
    }

    public static function insertTag(int $topic, string  $tag)
    {
        $insert='INSERT INTO `#{tags}` (`topic`, `name`, `count`) VALUES (:topic ,:tagname, 0);';
        if ((new Query($insert, ['topic'=>$topic, 'tagname'=>self::parseTag($tag)]))->exec()) {
            return Query::lastInsertId();
        }
        return 0;
    }

    public static function addTagToArticle(int $aid, int $topic, string $tag)
    {
        $add='INSERT INTO `#{article_tag}` (`aid`,`tid`)  SELECT :aid,:tid FROM DUAL WHERE NOT EXISTS
         (SELECT `aid`,`tid` FROM `#{article_tag}` WHERE aid=:aid AND tid=:tid );';
        $update='UPDATE `#{tags}` SET `count` = `count` + 1  WHERE `#{tags}`.`tid` = :tid ;';
        $tagid=0;
        // 修正Tag
        if ($c=self::getTagId($tag)) {
            $tagid=$c;
        } elseif ($i=self::insertTag($topic, $tag)) {
            $tagid=$i;
        } else {
            return false;
        }
        return (new Query($add, ['aid'=>$aid, 'tid'=>$tagid]))->exec() && (new Query($update, ['tid'=>$tagid]))->exec();
    }

    public static function setTagsToArticle(int $aid, int $topic, array $tag)
    {
        $old_tag=self::getTags($aid, $topic);
        $exist_tag=[];
        foreach ($old_tag as $key=>$tag_array) {
            // 存在的标签
            if (in_array($tag_array['name'], $tag)) {
                // 删除存在的
                unset($old_tag[$key]);
                $exist_tag[]=$tag_array['name'];
            }
        }
        // 添加没有的标签
        foreach ($tag as $tagname) {
            // 存在的标签
            if (!in_array($tagname, $exist_tag)) {
                var_dump(self::addTagToArticle($aid, $topic, $tagname));
            }
        }
        var_export($old_tag);
        // 删除不存在的标签
        foreach ($old_tag as $tag_array) {
            var_dump($tag_array);
            self::removeTag($aid, $tag_array['tid']);
        }
    }

    public static function getTags(int $aid, int $topic=0)
    {
        $q='SELECT `#{tags}`.`tid`, `name`  FROM `#{article_tag}` JOIN `#{tags}` ON `#{tags}`.`tid`=`#{article_tag}`.`tid` AND `#{tags}`.`topic`=:topic WHERE `#{article_tag}`.`aid` = :aid ORDER BY `atd_tags`.`count` DESC';
        $values=['topic'=>$topic, 'aid'=>$aid];
        return (new Query($q, $values))->fetchAll();
    }
    public static function getTagsInfo(int $topic=0)
    {
        return (new Query('SELECT * FROM `atd_tags`'))->fetchAll();
    }
    // 删除文件标签
    public static function removeTag(int $aid, int $tid)
    {
        // 删除标签
        $delete='DELETE FROM `#{article_tag}` WHERE `aid`=:aid AND `tid`=:tid LIMIT 1;';
        // 更新统计
        $update='UPDATE `#{tags}` SET `count` = `count`- 1  WHERE `#{tags}`.`tid` = :tid LIMIT 1;';
        return (new Query($delete, ['aid'=>$aid, 'tid'=>$tid]))->exec() && (new Query($update, ['tid'=>$tid]))->exec();
    }
    public static function parseTag(string $tag)
    {
        return preg_replace('/\s+?/', '-', $tag);
    }

    // 重新统计信息
    public function refresh()
    {
        $q='UPDATE `atd_tags` SET  `count`= (SELECT count(*) FROM `atd_article_tag` WHERE `atd_article_tag`.`tid` =`atd_tags`.`tid` ) WHERE 1;';
        return (new Query($q))->exec();
    }
    public function deleteEmpty()
    {
        self::refresh();
        return (new Query('DELETE FROM `atd_tags` WHERE `count`=0 ;'))->exec();
    }
}
