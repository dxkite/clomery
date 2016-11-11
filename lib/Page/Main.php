<?php
use Core\Value;

class Page_Main
{
    public function main(string $name='')
    {
        self::setNav();
        $this->run($name);
    }
    public function setNav()
    {
        Page::global('_Op', new Site_Options);
        Common_Navigation::init();
        $nav=Common_Navigation::getNavs();
        $user=Common_User::hasSignin();
        if ($user) {
            Page::set('has_signin', true);
            Page::set('user_info', new Value($user));
        } else {
            Page::set('has_signin', false);
        }

        Page::set('head_index_nav', $nav);
    }
}
