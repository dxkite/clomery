<?php

class View_Compiler_Pomelo
{
    public static $extRaw='.pml.html';
    public static $extCpl='.pml.php';
    protected static $errorInfo=[
        0=>'No Error',
        1=>'File %s Not Exist',
        2=>'Compile Include %s Error',
    ];
    protected static $error='';
    protected static $erron=0;
    protected static $echoTag=['{{','}}'];
    protected static $commentTag=['{--','--}'];
   
    protected static $theme='default';
    protected static $spider='spider';

    public static function viewPath(string $name)
    {
        $file=preg_replace('/[.|\\\\|\/]+/', DIRECTORY_SEPARATOR, $name);
        $path= APP_VIEW.'/'.$file.self::$extCpl;
        if (self::$theme==='spider')
        {
            $path=$path= APP_VIEW.'/@spider/'.$file.self::$extCpl;
        }
        return $path;
    }
    // 编译单文件
    public function compileFile(string $filename)
    {
        $file=preg_replace('/^'.preg_quote(APP_TPL.'/'.self::$theme.'/','/').'/','',$filename);
        if (Storage::exist($filename)) {
            $content= self::compileText(Storage::get($filename));
            $spider=self::$theme==='spider'?'/@spider/':'/';
            $output=APP_VIEW.$spider.preg_replace('/'.preg_quote(self::$extRaw).'$/',self::$extCpl,$file);
            // debug_print_backtrace();
            // var_dump($output);
            Storage::mkdirs(dirname($output));
            Storage::put($output, $content);
            return true;
        }
        self::$erron=1;
        self::$error=sprintf(self::$errorInfo[1], $filename);
        return false;
    }
    public function compileName(string $name)
    {
        $file=preg_replace('/[.|\\\\|\/]+/', DIRECTORY_SEPARATOR, $name);
        $filename=APP_TPL.'/'.self::$theme.'/'.$file.self::$extRaw;
        self::compileFile($filename);
    }
    // 编译文本
    public function compileText(string $text)
    {
        $result='';
        foreach (token_get_all($text) as $token) {
            if (is_array($token)) {
                list($tag, $content) = $token;
                // 所有将要编译的文本
                // 跳过各种的PHP
                if ($tag == T_INLINE_HTML) {
                    $content=self::compileString($content);
                    $content=self::compileCommand($content);
                }
                $result .=$content;
            } else {
                $result .=$token;
            }
        }
        return $result;
    }
    public function setThemes(string $theme='default')
    {
        self::$theme=$theme;
    }
    public function getTheme()
    {
        return self::$theme;
    }
    public function tplRoot()
    {
        if (self::$theme==='spider')
        {
            return APP_TPL.'/spider';
        }
        return APP_TPL.'/'.self::$theme;
    }
    private function compileString(string $str)
    {
        $callback=function ($match) {
            // var_dump($match);
            if (method_exists($this, $method = 'parse'.ucfirst($match[1]))) {
                $match[0] = $this->$method(isset($match[3])?$match[3]:null);
            }
            return isset($match[3]) ? $match[0] : $match[0].$match[2];
        };
        return preg_replace_callback('/\B@(\w+)(\s*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $str);
    }

    private function compileCommand(string $str)
    {
        $orecho=sprintf('/%s\s*?(?=\$)(.+?)\s+or\s+(.+?)\s*?%s/', self::$echoTag[0], self::$echoTag[1]);
        $echo=sprintf('/%s(.+)%s/', self::$echoTag[0], self::$echoTag[1]);
        $comment=sprintf('/%s(.+)%s/', self::$commentTag[0], self::$commentTag[1]);
        // var_dump($orecho);
        return preg_replace(
            [$orecho,$echo, $comment],
            ['<?php Env::echo(isset($1) ? $1 : $2) ?>','<?php Env::echo($1) ?>', '<?php /* $1 */ ?>'],
            $str
        );
    }

    // IF 语句
    protected function parseUrl($exp)
    {
        return "<?php echo Page::url{$exp} ?>";
    }
    // IF 语句
    protected function parseIf($exp)
    {
        return "<?php if{$exp}: ?>";
    }
    protected function parseEndif()
    {
        return '<?php endif; ?>';
    }
    protected function parseElse()
    {
        return '<?php else: ?>';
    }

    protected function parseElseif($exp)
    {
        return "<?php elseif {$exp}: ?>";
    }
    // for
    protected function parseFor($expression)
    {
        return "<?php for{$expression}: ?>";
    }
    protected function parseEndfor()
    {
        return '<?php endfor; ?>';
    }
    // foreach
    protected function parseForeach($exp)
    {
        return "<?php foreach{$exp}: ?>";
    }
    protected function parseEndforeach()
    {
        return '<?php endforeach; ?>';
    }
    // while
    protected function parseWhile($exp)
    {
        return "<?php while{$exp}: ?>";
    }
    protected function parseEndwhile()
    {
        return '<?php endwhile; ?>';
    }
    // include
    protected function parseInclude($exp, array $includes=[])
    {
        preg_match('/^\(([\'"])(.+)(?1)/', $exp, $match);
        foreach ($includes as $path) {
            $compile=self::compileFile($path.'/'.$match[2]);
        }
        return "<?php Env::include{$exp} -> render(); ?>";
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
