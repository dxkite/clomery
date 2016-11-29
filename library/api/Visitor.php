<?php
namespace api;

use Page;
use Request;


abstract class Visitor
{
    public function main(Request $request)
    {
        // TODO 限制权限
        Page::json();
        return (new $this->class)->apiMain(new Param($request->json()));
    }
}
