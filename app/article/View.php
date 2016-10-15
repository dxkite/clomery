<?php
namespace article;

use ArticleManager;
use Markdown\Parser as Markdown_Parser;
use Page;
use Core\Value as CoreValue;

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
        Page::set('article_list', ArticleManager::getArticlesList(0, $page_content, (int)$offset));
        Page::set('article_numbers', $page_number=ArticleManager::numbers());
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
    public static function article($page)
    {
        import('Site.functions');
        \Site\page_common_set();
        Page::set('head_index_nav_select', 1);
        $info=ArticleManager::getArticleInfo((int)$page);
        Page::set('title', $info['title']);
        Page::set('article', new CoreValue($info));
        Page::use('article/read');
        $p=new Markdown_Parser;
        $c=ArticleManager::getArticleContent((int)$page);
        Page::set('article_html', $p->makeHTML($c));
    }
}
