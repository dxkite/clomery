<?php
class View
{
    private static $compiler=null;
    public static function loadCompile()
    {
        if (is_null(self::$compiler)) {
            $compiler='View_Compiler_'. conf('Driver.View', 'Pomelo');
            self::$compiler=new $compiler;
        }
    }
    
    public static function theme(string $theme=null)
    {
        if (is_null($theme)) {
            return self::$compiler->getTheme();
        }
        self::$compiler->setTheme($theme);
    }

    public static function viewPath(string $name):string
    {
        return self::$compiler->viewPath($name);
    }

   
    public static function tplRoot()
    {
        return self::$compiler->tplRoot();
    }
    public static function compile($input)
    {
        return self::$compiler->compileFile($input);
    }
    public static function compileAll(string $theme='default')
    {
        if (self::$compiler)
        {
            $theme=self::$compiler->getTheme();
        }
        
        $files=Storage::readDirFiles(APP_TPL.'/'.$theme, '/\.pml\.html$/', true);
        Storage::rmdirs(APP_VIEW,true);
        foreach ($files as $file) {
            View::compile($file);
        }
        $extensions='';
        foreach (array_keys (mime()) as $ext)
        {
            $extensions.='|'.$ext;
        }
        $extensions=trim($extensions,'|');
        $resources=Storage::readDirFiles(APP_TPL.'/'.$theme,'/(?<!\.pml)\.('.$extensions.')$/',true,false);
        foreach ($resources as $resource)
        {
            $path=APP_VIEW.'/'.$resource;
            Storage::mkdirs(dirname($path));
            Storage::copy(APP_TPL.'/'.$theme.'/'.$resource,$path);
        }
    }
}
