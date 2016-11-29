<?php
namespace api;

use Page;
use Request;

abstract class Visitor
{
    public function main(Request $request)
    {
        Page::json();
        $ctr=new $this->class;
        return $ctr->apiMain(new Param($request->json()));
    }
}
