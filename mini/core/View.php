<?php
class View
{
    public static $compiler=null;
    private function loadCompile()
    {
        if (is_null(self::$compiler)) {
            $compiler='View_Compiler_'. mini('Driver.View', 'Pomelo');
            self::$compiler=new $compiler;
        }
    }
    public static function  test($input,$output)
    {
        self::loadCompile();
        $content=self::$compiler->compileFile($input);
        Storage::put($output,$content);
        return $content;
    }
}
