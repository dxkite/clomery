<?php
Page::getController()->raw()->noCache();
Common_User::signout();
Page::redirect('/');
