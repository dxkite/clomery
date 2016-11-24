<?php
namespace server;

class Render
{
    protected $page;
    function __construct(Page $page)
    {
        $this->page=$page;
    }
    
}