<?php
namespace article;

use DB_Article;
use Markdown\Parser as Markdown_Parser;
use Page;
use Core\Value as CoreValue;
use DB_Tag;

class View
{
    public static function list($offset=0)
    {
        import('Site.functions');
        \Site\page_common_set();
        Page::set('head_index_nav_select', 1);
        $page_content=2;
        $page= (int) ($offset/$page_content+1);
        $title= $page?'- 第'.$page .'页':'';
        Page::set('title', '文章'.$title);
        Page::use('article/index');

        $article_list=DB_Article::getArticlesList(0, $page_content, (int)$offset);
        $article_list_obj=[];

        foreach ($article_list as $article){
            $article_list_obj[]= new CoreValue($article);
        }

        Page::set('article_list', $article_list_obj);

        Page::set('article_numbers', $page_number=DB_Article::numbers());

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
        $info=DB_Article::getArticleInfo((int)$aid);
        Page::set('title', $info['title']);
        $info['tags']=DB_Tag::getTags((int)$aid);
        Page::set('article', new CoreValue($info));
        Page::use('article/read');
        $p=new Markdown_Parser;
        $c=DB_Article::getArticleContent((int)$aid);
        Page::set('article_html', $p->makeHTML($c));
    }
}
