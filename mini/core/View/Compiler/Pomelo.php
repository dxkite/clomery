<?php

class View_Compiler_Pomelo
{
    protected static $ext='.pomelo.html';
    protected static $error=[
        0=>'No Error',
        1=>'Include Too Deep',
    ];
    protected static $erron=0;
    protected static $echoTag=['{{','}}'];
    protected static $commentTag=['{--','--}'];
    
    // 编译单文件
    public function compileFile(string $file, array $include_path=[])
    {
        if (Storage::exist($file)) {
            return self::compileText(Storage::get($file));
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
        foreach (token_get_all($text) as $token) {
            if (is_array($token)) {
                list($tag, $content) = $token;
                // 所有将要编译的文本
                // 跳过各种的PHP
                if ($tag == T_INLINE_HTML) {
                    $content=self::compileString($content, $include_path);
                }
                $result .=$content;
            } else {
                $result .=$token;
            }
        }
        return $result;
    }

    private function compileString(string $str, array $include_path=[])
    {
        $callback=function ($match) use ($include_path) {
            if (method_exists($this, $method = 'parse'.ucfirst($match[1]))) {
                $match[0] = $this->$method(isset($match[3])?$match[3]:null, $include_path);
            }
            return isset($match[3]) ? $match[0] : $match[0].$match[2];
        };
        return preg_replace_callback('/\B@(\w+)(\s*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $str);
    }

    // IF 语句
    private function parseIf($exp)
    {
        return "<?php if{$exp}: ?>";
    }
    private function parseEndif()
    {
        return '<?php endif; ?>';
    }
    private function parseElse()
    {
        return '<?php else: ?>';
    }

    private function parseElseif($exp)
    {
        return "<?php elseif {$exp}: ?>";
    }

    private function parseInclude($exp, array $includes=[])
    {
        return count($includes)?
         "<?php Env::include{$exp} -> path(".var_export($includes, true).') -> rander(); ?>':
         "<?php Env::include{$exp} -> rander(); ?>";
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
