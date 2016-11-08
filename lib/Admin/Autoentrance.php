<?php
use Core\Value;

abstract class Admin_Autoentrance
{
    public function main()
    {
        if (\System::user()->hasSignin) {
            if (\System::user()->permission->editSite) {
                self::setBasic();
                $this->run();
            } else {
                echo 'no perimission';
                Page::redirect('/');
            }
        } else {
            Page::redirect('/user/SignIn');
        }
    }
    abstract function run();

    public function setBasic()
    {
        $options[]=new Value(['title'=>'网站信息', 'href'=>Page::url('admin')]);
        $options[]=new Value(['title'=>'网站设置', 'href'=>Page::url('admin', ['path'=>'Site'])]);
        $options[]=new Value(['title'=>'导航栏设置', 'href'=>Page::url('admin', ['path'=>'Navigation'])]);
        $options[]=new Value(['title'=>'用户管理', 'href'=>Page::url('admin', ['path'=>'User'])]);
        Page::set('options', $options);
        Page::use('admin/index');
    }
}