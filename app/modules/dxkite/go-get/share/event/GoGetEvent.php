<?php

namespace dxkite\goget\event;


use suda\framework\Config;
use suda\framework\Request;
use suda\framework\Response;
use suda\application\Application;
use suda\framework\runnable\Runnable;
use dxkite\goget\processor\GoGetProcessor;

class GoGetEvent
{
    /**
     * @var Runnable
     */
    protected static $default;

    /**
     * @var Application
     */
    protected static $app;

    public static function injectGoLangProcessor(Config $config, Application $app)
    {
        $goget = $app->conf('go-get');
        $processor = $config->get('processor', []);
        $config->set('processor', array_merge([GoGetProcessor::class], $processor));

        if ($goget !== null) {
            static::$default = $app->route()->getDefaultRunnable();
            static::$app = $app;
            $app->route()->default(__CLASS__.'::defaultRunnable');
            $config->set('processor', array_merge([GoGetProcessor::class], $processor));
        }
    }

    public static function defaultRunnable(Request $request, Response $response) {
        if ($request->get('go-get') == 1) {
            return static::getGoGet(static::$app, $request);
        }
        if (static::$app->conf('go-get.only', false) == true) {
            $response->status(503);
            return '';
        }
        return GoGetEvent::$default->run($request, $response);
    }

    public static function getGoGet(Application $application, Request $request) {
        $goget = $application->conf('go-get');
        if ($goget === null) {
            return 'Go Get Config Miss';
        }
        $template = $application->getTemplate('dxkite/go-get:go-get', $request);
        $template->assign($goget);
        $template->set('host', $request->getHost());
        return $template;
    }
}