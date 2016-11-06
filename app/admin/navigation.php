<?php
if (isset($_POST['nav_set']))
{
    foreach($_POST['nav_set'] as $id=> $set)
    {
        Common_Navigation::update($id,$set);
        echo '设置成功:'.$id;
    }
    header('Location:'.$_SERVER['PHP_SELF']);
}
Page::set('navigations',Common_Navigation::getNavset());
Page::use('admin/navigation');