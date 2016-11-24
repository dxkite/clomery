<?php

require_once __DIR__.'/../system/autoload.php';
require_once __DIR__.'/../system/definition.php';
(new Router(new Request()))->dispatch();