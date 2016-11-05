<?php

/**
 * Class View
 */
class View
{
    /**
     * 模板编译器
     * @var null
     */
    private static $compiler=null;

    /**
     * 载入模板编译器
     */
    public static function loadCompile()
    {
        if (is_null(self::$compiler)) {
            $compiler='View_Compiler_'. conf('Driver.View', 'Pomelo');
            self::$compiler=new $compiler;
        }
    }

    /**
     * 获取/设置模板样式
     * @param string|null $theme
     * @return mixed
     */
    public static function theme(string $theme=null)
    {
        if (is_null($theme)) {
            return self::$compiler->getTheme();
        }
        self::$compiler->setTheme($theme);
    }

    /**
     * 获取编译后的模板目录
     * @param string $name
     * @return string
     */
    public static function viewPath(string $name):string
    {
        return self::$compiler->viewPath($name);
    }


    /**
     * 获取模板根目录
     * @return mixed
     */
    public static function tplRoot()
    {
        return self::$compiler->tplRoot();
    }

    /**
     * 编译文件
     * @param $input
     * @return mixed
     */
    public static function compile($input)
    {
        return self::$compiler->compileFile($input);
    }

    /**
     * 编译全部的文件
     * @param string $theme
     */
    public static function compileAll(string $theme='default')
    {
        Storage::rmdirs(APP_VIEW, true);
        if (self::$compiler) {
            $theme=self::$compiler->getTheme();
        }
        $files=Storage::readDirFiles(APP_TPL.'/'.$theme, true, '/\.pml\.html$/');
        foreach ($files as $file) {
            View::compile($file);
        }
        $extensions='';
        foreach (array_keys(mime()) as $ext) {
            $extensions.='|'.$ext;
        }
        $extensions=trim($extensions, '|');
        $resources=Storage::readDirFiles(APP_TPL.'/'.$theme, true, '/(?<!\.pml)\.('.$extensions.')$/');
        $len=strlen(APP_TPL.'/'.$theme);
        foreach ($resources as $resource) {
            $path=APP_VIEW.'/'.substr($resource, $len+1);
            Storage::mkdirs(dirname($path));
            Storage::copy($resource, $path);
        }
    }
    public function file($path_raw)
    {
        $type=pathinfo($path_raw, PATHINFO_EXTENSION);
        $path_raw=rtrim($path_raw, '/');
        if (Storage::exist(APP_VIEW.'/'.$path_raw)) {
            Page::getController()->raw()->type($type);
            echo Storage::get(APP_VIEW.'/'.$path_raw);
        } else {
            Page::error404($path_raw);
        }
    }
}
