<?php
class View
{
    private static $compiler=null;
    private static $values=[];

    private function loadCompile()
    {
        if (is_null(self::$compiler)) {
            $compiler='View_Compiler_'. mini('Driver.View', 'Pomelo');
            self::$compiler=new $compiler;
        }
    }

    public static function render(string $page,array $values=[])
    {
        
    }
    
    public static function  compile($input,$output)
    {
        self::loadCompile();
        $content=self::$compiler->compileFile($input);
        return Storage::put($output,$content);
    }
}
