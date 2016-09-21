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

    public static function render(string $page, array $values=[])
    {
        self::loadCompile();
        $values=array_merge($values,self::$values);
        // 分解变量
        extract($values, EXTR_OVERWRITE);
        $file=self::$compiler->getViewPath($page);
        if (Storage::exist($file))
            require_once $file;
        else
            echo $page.' TPL NO FIND!'; 
    }
    
    public static function compile($input)
    {
        self::loadCompile();
        $content=self::$compiler->compileFile($input);
    }
}
