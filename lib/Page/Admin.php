<?php
use Core\Value;

abstract class Page_Admin
{
    public function main()
    {
        self::setBasic();
        $this->run();
    }
    public function setBasic()
    {
        $options[]=new Value(['title'=>'网站信息', 'href'=>Page::url('admin')]);
        $options[]=new Value(['title'=>'网站设置', 'href'=>Page::url('admin', ['path'=>'Site'])]);
        $options[]=new Value(['title'=>'导航栏设置', 'href'=>Page::url('admin', ['path'=>'Navigation'])]);
        $options[]=new Value(['title'=>'用户管理', 'href'=>Page::url('admin', ['path'=>'User'])]);
        $options[]=new Value(['title'=>'文章管理', 'href'=>Page::url('admin', ['path'=>'Article'])]);
        Page::set('options', $options);
        Page::use('admin/index');
    }
}
