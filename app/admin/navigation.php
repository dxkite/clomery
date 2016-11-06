<?php
if (isset($_POST['nav_set']))
{
    foreach($_POST['nav_set'] as $id=> $set)
    {
        Common_Navigation::update($id,$set);
        echo '设置成功:'.$id;
    }
}
Page::set('navigations',Common_Navigation::getNavset());
Page::use('admin/navigation');