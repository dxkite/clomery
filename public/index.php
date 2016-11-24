<?php

require_once (__DIR__.'/../system/initailze.php');

(new server\Router(new server\Request()))->dispatch();