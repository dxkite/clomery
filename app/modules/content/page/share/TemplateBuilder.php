<?php


namespace content\page;


use ArrayAccess;
use suda\application\template\ModuleTemplate;
use suda\framework\arrayobject\ArrayDotAccess;

class TemplateBuilder
{
    /**
     * @var ModuleTemplate
     */
    protected $template;


    /**
     * TemplateBuilder constructor.
     * @param ModuleTemplate $template
     */
    public function __construct(ModuleTemplate $template)
    {
        $this->template = $template;
    }

    /**
     * @param array|ArrayAccess $data
     * @param string $name
     */
    public function apply($data, string $name)
    {
        $this->template->set($name, $data);
    }

    /**
     * @param array|ArrayAccess $data
     * @param array $assign
     */
    public function assign($data, array $assign)
    {
        foreach ($assign as $from => $to) {
            $item = ArrayDotAccess::get($data, $from);
            $this->template->set($to, $item);
        }
    }
}