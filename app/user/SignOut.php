<?php
Page::getController()->raw()->noCache();
UManager::signout();
Page::redirect('/');
