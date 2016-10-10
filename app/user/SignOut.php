<?php
Page::getController()->raw()->noCache();
UManager::signout();
echo '退出登陆';
