<?php
Page::getController()->raw()->noCache();
UManager::signout();
Page::redirect('/');
echo '退出登陆';
