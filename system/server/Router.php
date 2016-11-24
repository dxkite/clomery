<?php
namespace server;

class Router
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request=$request;
    }
    public function dispatch()
    {
        echo 'dispatch';
    }
}
