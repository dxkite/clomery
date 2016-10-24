<?php

class Blog_Category
{
    public function createCategorys(string $category_str)
    {
        $categorys=self::toArray($category_str);
        $parent=0;
        foreach ($categorys as $category) {
            $parent=self::quickCreateCategory($category, $parent);
        }
        return $parent;
    }

    public function toArray(string $category)
    {
        return preg_split('/(\s*>\s*|\s+)/', trim($category));
    }
    
    // 设置分类
    public function setCategory(int $aid, string $categorys)
    {
        $arrays=self::toArray($categorys);
        $category=self::getCategoryId(end($arrays));
        var_dump('categoryid='.$category.'-for:'.end($arrays));
        $q='UPDATE `atd_articles` SET `category` = :category WHERE `atd_articles`.`aid` = :aid ;';
        $u='UPDATE `atd_category` SET `count` = `count` + 1  WHERE `atd_category`.`cid` = :cid;';
        return (new Query($q, ['aid'=>$aid, 'category'=>$category]))->exec() && (new Query($u, ['cid'=>$category]))->exec();
    }

    // 创建分类
    public function createCategory(int $icon_id, string $name, string $discription, int $parent=0)
    {
        $q='INSERT INTO `atd_category` (`icon`, `name`, `discription`,`parent`) VALUES (:icon, :name , :discription , :parent);';
        if ((new Query($q, ['icon'=>$icon_id, 'name'=>$name, 'discription'=>$discription, 'parent'=>$parent]))->exec()) {
            return Query::lastInsertId();
        }
        return 0;
    }
    // 快速创建分类
    public function quickCreateCategory(string $name, int $parent=0)
    {
        $q='INSERT INTO `atd_category` (`name`, `discription`,`parent`) VALUES (:name , :discription , :parent);';
        if ((new Query($q, ['name'=>$name, 'discription'=>$name, 'parent'=>$parent]))->exec()) {
            return Query::lastInsertId();
        }
        return 0;
    }

    public function deleteCategory(int $cid)
    {
        $d='DELETE FROM `atd_category` WHERE `cid`=:cid LIMIT 1;';
        $dp='UPDATE FROM `atd_category` SET `parent`=0 WHERE `parent`=:cid';
        $da='UPDATE FROM `atd_articles` SET `category` = 0 WHERE `category` = :cid ;';
        return (new Query($d, ['cid'=>$cid]))->exec() && (new Query($dp, ['cid'=>$cid]))->exec() && (new Query($da, ['cid'=>$cid]))->exec();
    }

    public function getCategoryId(string $name)
    {
        $q='SELECT `cid` FROM `atd_category` WHERE `name`=:name LIMIT 1;';
        if ($get=(new Query($q, ['name'=>$name]))->fetch()) {
            return $get['cid'];
        }
        return  0;
    }
    public function getCategorysInfo()
    {
        $q='SELECT * FROM `atd_category`';
        return (new Query($q))->fetchAll();
    }
    
    public function getCategoryInfo(int $cid)
    {
        $q='SELECT * FROM `atd_category` WHERE `cid`=:cid';
        return (new Query($q, ['cid'=>$cid]))->fetch();
    }
    // 重新统计分类信息
    public function refresh()
    {
        $q='UPDATE `atd_category` SET  `count`= (SELECT count(*) FROM atd_articles WHERE `category` =`atd_category`.`cid` ) WHERE 1;';
        return (new Query($q))->exec();
    }
}
