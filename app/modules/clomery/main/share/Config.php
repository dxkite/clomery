<?php


namespace clomery\main;


use suda\application\Application;
use suda\application\Resource;

class Config extends \suda\framework\Config
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var array
     */
    protected $config;

    /**
     * MetaData constructor.
     * @param Application $application
     */
    public function __construct(Application $application, string $name)
    {
        parent::__construct([]);
        $this->application = $application;
        $this->loadDataConfig($name);
    }

    protected function loadDataConfig(string $name) {
        $config = [];
        $data = new Resource($this->application->getDataPath());
        $path = $data->getConfigResourcePath('config/'.$name);
        if ($path !== null) {
            $this->load($path);
        }
        return $config;
    }
}