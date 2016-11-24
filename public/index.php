<?php

require_once __DIR__.'/../system/initailze.php';

Router::dispatch(new Request());
Cookie::write();