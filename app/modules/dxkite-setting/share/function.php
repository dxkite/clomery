<?php
use cn\atd3\setting\Setting;

function setting(string $name,$default=null) {
    return Setting::get($name,$default);
}

function setting_val(string $name,$value) {
    return Setting::set($name,$value);
}