<?php

interface Entrance
{
    public static function beforeRun(Request $request);
    public static function main(Request $request);
    public static function afterRun($return);
}
