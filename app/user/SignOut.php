<?php
Page::getController()->raw()->noCache();
DB_User::signout();
Page::redirect('/');
