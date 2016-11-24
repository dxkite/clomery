<?php

require_once __DIR__.'/../system/initailze.php';

(new server\Router(new Request()))->dispatch();