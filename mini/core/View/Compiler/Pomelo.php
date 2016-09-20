<?php

class View_Compile_Pomelo
{
    public static $ext='.pomelo.html';
    public static $error=[
        0=>'No Error',
        1=>'Include Too Deep',
    ];
    public static $erron=0;
    // 编译单文件
    public function compileFile(string $file, array $include_path=[])
    {
        if(Storage::exist($file))
        {
            
        }
        return false;
    }
    // 编译多文件
    public function compileFiles(array $files, array $include_path=[])
    {
        
    }
    // 编译文本
    public function compileText(string $text, array $include_path=[])
    {
        $result='';
        foreach (token_get_all($value) as $token) {
            if (is_array($token)) {
                list($tag, $content) = $token;
                // 所有将要编译的文本
                if ($tag == T_INLINE_HTML) {
                    $content=self::compileString($content,$include_path);
                }
                $result .=$content;
            } else {
                $result .=$token;
            }
        }
        return $result;
    }

    private function compileString(string $str,array $include_path=[])
    {

    }
    // 错误报错
    public function error()
    {
        return self::$error[self::$erron];
    }
    // 错误码
    public function erron()
    {
        return self::$erron;
    }
}
