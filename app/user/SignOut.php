<?php
Page::getController()->raw()->noCache();
UserManager::signout();
Page::redirect('/');
