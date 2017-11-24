<?php
namespace cn\atd3\response;

use cn\atd3\visitor\Context;

class SettingResponse extends \cn\atd3\user\response\OnUserVisitorResponse
{
    public static $continents = [ 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'];
    /**
     * 
     * @acl list_setting
     * @param Context $context
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $page=$this->page('setting');
        $page->set('timezone_list', timezone_identifiers_list());
        $page->render();
    }

    public function getTimezoneOptions()
    {
        $timezone=[];
        foreach (timezone_identifiers_list() as $item) {
            $zone=explode('/', $item);
            if (!in_array($zone[0], self::$continents)) {
                continue;
            }
            $timezone[$zone[0]][]=['name'=>$zone[1],'value'=>$item];
        }
        $select=setting('timezone', 'PRC');
        if ($select=='PRC') {
            $list[] = '<option selected="selected" value="PRC">' . __('PRC') . '</option>';
        } else {
            $list[] = '<option value="PRC">' . __('PRC') . '</option>';
        }
        if ($select=='UTC') {
            $list[] = '<option  selected="selected" value="UTC">' . __('UTC') . '</option>';
        } else {
            $list[] = '<option value="UTC">' . __('UTC') . '</option>';
        }
        
        foreach ($timezone as $name=>$continents) {
            $list[]= '<optgroup label="'.__($name).'">';
            foreach ($continents as $city) {
                if ($select == $city['value']) {
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
