<?php
namespace article;

use Blog_Article;
use Markdown\Parser as Markdown_Parser;
use Page;
use View\Value;
use Blog_Tag;
use Blog_Category;

class View
{
    public static function list($offset=0)
    {
        var_dump($offset);
        \Page_Main::setNav();
        Page::set('head_index_nav_select', 1);
        // TODO : 添加文章管理
        $page_content=10;
        $page= (int) ($offset/$page_content+1);
        $title= $page?'- 第'.$page .'页':'';
        Page::set('title', '文章'.$title);
        Page::use('article/index');

        $article_list=Blog_Article::getArticlesList(0, $page_content, (int)$offset);
        $article_list_obj=[];

        foreach ($article_list as $article) {
            $article_list_obj[]= new Value($article);
        }

        Page::set('article_list', $article_list_obj);

        Page::set('article_numbers', $page_number=Blog_Article::countPublic());
        
        if ($page_content<$page_number) {
            for ($i=0, $j=1;$i<$page_number;$j++, $i+=$page_content) {
                $pages[$j]['offset']=$i;
                if ($i == $offset) {
                    $pages[$j]['current']=true;
                }
            }
            Page::set('page_nav', $pages);
            Page::set('use_page_nav', true);
        } else {
            Page::set('use_page_nav', false);
        }
    }

    public static function listCategory($category, $offset=0)
    {
        var_dump("id====".$category);
        \Page_Main::setNav();
        Page::set('head_index_nav_select', 1);
        $page_content=2;
        $page= (int) ($offset/$page_content+1);
        $title= $page?'- 第'.$page .'页':'';
        Page::set('title', $category.$title);
        Page::use('article/category');
        
        $article_list=Blog_Article::getArticlesListbyCategory(0, $cid=Blog_Category::getCategoryId($category), $page_content, (int)$offset);
        Page::set('category', $category);
        // var_dump($article_list,Blog_Category::getCategoryId($category));
        $article_list_obj=[];

        foreach ($article_list as $article) {
            $article_list_obj[]= new Value($article);
        }

        Page::set('article_list', $article_list_obj);

        Page::set('article_numbers', $page_number=Blog_Category::getCategoryInfo($cid)['count']);
        
        if ($page_content<$page_number) {
            for ($i=0, $j=1;$i<$page_number;$j++, $i+=$page_content) {
                $pages[$j]['offset']=$i;
                if ($i == $offset) {
                    $pages[$j]['current']=true;
                }
            }
            Page::set('page_nav', $pages);
            Page::set('use_page_nav', true);
        } else {
            Page::set('use_page_nav', false);
        }
    }
    public static function listTag($tagname, $offset=0)
    {
        Page::set('head_index_nav_select', 1);
        \Page_Main::setNav();
        $page_content=2;
        $page= (int) ($offset/$page_content+1);
        $title= $page?'- 第'.$page .'页':'';
        Page::set('title', $tagname .$title);
        Page::use('article/tag');
        
        $article_list=Blog_Article::getArticlesListByTag(0, Blog_Tag::getTagId($tagname), $page_content, (int)$offset);
        Page::set('tag', $tagname);
        $article_list_obj=[];
        foreach ($article_list as $article) {
            $article_list_obj[]= new Value($article);
        }
        Page::set('article_list', $article_list_obj);
        Page::set('article_numbers', $page_number=Blog_Tag::getInfoByName($tagname)['count']);
        if ($page_content<$page_number) {
            for ($i=0, $j=1;$i<$page_number;$j++, $i+=$page_content) {
                $pages[$j]['offset']=$i;
                if ($i == $offset) {
                    $pages[$j]['current']=true;
                }
            }
            Page::set('page_nav', $pages);
            Page::set('use_page_nav', true);
        } else {
            Page::set('use_page_nav', false);
        }
    }
    public static function article($aid)
    {
        $info=Blog_Article::getArticleInfo((int)$aid);
        \Page_Main::setNav();
        Page::set('head_index_nav_select', 1);
        Page::set('title', $info['title']);
        $info['tags']=Blog_Tag::getTags((int)$aid);
        Page::set('article', new Value($info));
        Page::use('article/read');
        if ($info['verify']) {
            $p=new Markdown_Parser;
            $p->hook('afterParseCode', function ($result, $value) {
                if (preg_match('/^<pre><code class="(.+?)"/', $result)) {
                    return preg_replace('/^<pre><code class="(.+?)"/', '<pre><code class="prettyprint lang-$1"', $result);
                }
                return preg_replace('/^<pre><code>/', '<pre><code class="prettyprint">', $result);
            });
            $c=Blog_Article::getArticleContent((int)$aid);
            Page::set('article_html', $p->makeHTML($c));
        } else {
            Page::set('article_html', '文章审核中...');
        }
    }
}
