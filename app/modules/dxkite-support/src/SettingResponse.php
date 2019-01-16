<?php
namespace dxkite\support\response;

use dxkite\support\visitor\Context;

class SettingResponse extends \dxkite\support\setting\Response
{
    public static $continents = [ 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'];
    /**
     *
     * @acl website.listSetting
     * @param Context $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $view->set('title', __('网站设置'));
    }

    public function adminContent($template)
    {
        $template->include(module(__FILE__).':setting');
    }
    
    public function getTimezoneOptions()
    {
        $timezone = $this->settingProvider->getTimezone();
        foreach ($timezone as $name=>$continents) {
            $list[]= '<optgroup label="'.__($name).'">';
            foreach ($continents as $city) {
                if (\array_key_exists('select', $city) &&  $city['select']) {
                    $list[]='<option value="'.$city['value'].'" selected="selected">' .__($name). '/'. __($city['name']) . '</option>';
                } else {
                    $list[]='<option value="'.$city['value'].'">'.__($name). '/'.__($city['name']) . '</option>';
                }
            }
            $list[] = '</optgroup>';
        }
        return join("\n", $list);
    }
}
