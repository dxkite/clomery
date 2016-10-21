<?php
namespace article;

use Blog_Article;
use Markdown\Parser as Markdown_Parser;
use Page;
use Core\Value as CoreValue;
use Blog_Tag;
use Blog_Category;

class View
{
    public static function list($offset=0)
    {
        import('Site.functions');
        \Site\page_common_set();
        Page::set('head_index_nav_select', 1);
        // TODO : 添加博客管理后台 
        // TODO : 添加文章管理
        $page_content=10;
        $page= (int) ($offset/$page_content+1);
        $title= $page?'- 第'.$page .'页':'';
        Page::set('title', '文章'.$title);
        Page::use('article/index');

        $article_list=Blog_Article::getArticlesList(0, $page_content, (int)$offset);
        $article_list_obj=[];

        foreach ($article_list as $article){
            $article_list_obj[]= new CoreValue($article);
        }

        Page::set('article_list', $article_list_obj);

        Page::set('article_numbers', $page_number=Blog_Article::numbers());
        
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

    public static function listCategory($category,$offset=0)
    {
        import('Site.functions');
        
        \Site\page_common_set();

        Page::set('head_index_nav_select', 1);
        // TODO : 添加博客管理后台 
        // TODO : 添加文章管理
        $page_content=2;
        $page= (int) ($offset/$page_content+1);
        $title= $page?'- 第'.$page .'页':'';
        Page::set('title', $category.$title);
        Page::use('article/category');
        
        $article_list=Blog_Article::getArticlesListbyCategory(0,Blog_Category::getCategoryId($category), $page_content, (int)$offset);
         Page::set('category',$category);
        // var_dump($article_list,Blog_Category::getCategoryId($category));
        $article_list_obj=[];

        foreach ($article_list as $article){
            $article_list_obj[]= new CoreValue($article);
        }

        Page::set('article_list', $article_list_obj);

        Page::set('article_numbers', $page_number=Blog_Article::numbers());
        
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
        import('Site.functions');
        \Site\page_common_set();
        Page::set('head_index_nav_select', 1);
        $info=Blog_Article::getArticleInfo((int)$aid);
        Page::set('title', $info['title']);
        $info['tags']=Blog_Tag::getTags((int)$aid);
        Page::set('article', new CoreValue($info));
        Page::use('article/read');
        $p=new Markdown_Parser;
        $c=Blog_Article::getArticleContent((int)$aid);
        Page::set('article_html', $p->makeHTML($c));
    }
}
