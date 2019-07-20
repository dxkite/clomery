<?php


namespace clomery\main\data;


use clomery\main\Config;
use suda\application\template\Template;

class ConfigData
{
    /**
     * @param Template $template
     */
    public static function website($template) {
        $data = new Config($template->getApplication(), 'meta-data');
        $template->set('website', $data->get('website', []));
    }

    /**
     * @param Template $template
     */
    public static function socialLinks($template)
    {
        $data = new Config($template->getApplication(), 'meta-data');
        $template->set('socialLinks', $data->get('social-links', []));
    }

    /**
     * @param Template $template
     */
    public static function menu($template)
    {
        $data = new Config($template->getApplication(), 'meta-data');
        $template->set('menu', $data->get('menu', []));
    }

    /**
     * @param Template $template
     */
    public static function profile($template)
    {
        $data = new Config($template->getApplication(), 'meta-data');
        $template->set('profile', $data->get('profile', []));
    }
}