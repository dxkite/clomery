<?php

class View_Compiler_Pomelo
{
    public static $ext='.pomelo.html';
    protected static $errorInfo=[
        0=>'No Error',
        1=>'File %s Not Exist',
        2=>'Compile Include %s Error',
    ];
    protected static $error='';
    protected static $erron=0;
    protected static $echoTag=['{{','}}'];
    protected static $commentTag=['{--','--}'];
    
    // 编译单文件
    public function compileFile(string $file, array $include_path=[])
    {
        if (Storage::exist($file)) {
            return self::compileText(Storage::get($file));
        }
        self::$erron=1;
        self::$error=sprintf(self::$errorInfo[1],$file);
        return false;
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
                    $content=self::compileCommand($content);
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

    private function compileCommand(string $str)
    {
        $echo=sprintf('/%s(.+)%s/',self::$echoTag[0],self::$echoTag[1]);
        $comment=sprintf('/%s(.+)%s/',self::$commentTag[0],self::$commentTag[1]);
        return preg_replace(
            [$echo,$comment],
            ['<? Env::echo(\\1) ?>','<?php /* \\1 */ ?>'],
            $str
        );
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
        preg_match('/^\(([\'"])(.+)(?1)/',$exp,$match);
        foreach ($includes as $path)
        {
            $compile=self::compileFile($path.'/'.$match[2]);
        }
        return "<?php Env::include{$exp} -> rander(); ?>";
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
