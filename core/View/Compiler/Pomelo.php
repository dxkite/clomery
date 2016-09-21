<?php

class View_Compiler_Pomelo
{
    public static $extRaw='.pomelo.html';
    public static $extCpl='.pomelo.php';
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

    public static function getViewPath(string $name)
    {
        $file=preg_replace('/[.|\\\\|\/]+/', DIRECTORY_SEPARATOR, $name);
        return APP_VIEW.'/'.$file.self::$extCpl;
    }
    // 编译单文件
    public function compileFile(string $file)
    {
        $file=preg_replace('/[.|\\\\|\/]+/', DIRECTORY_SEPARATOR, $file);
        $filename=APP_TPL.'/'.self::$theme.'/'.$file.self::$extRaw;
        if (Storage::exist($filename)) {
            $content= self::compileText(Storage::get($filename));
            $output=APP_VIEW.'/'.$file.self::$extCpl;
            // if (!Storage::exsit($output)) {
            Storage::mkdirs(dirname($output));
            Storage::put($output, $content);
            // }
            return true;
        }
        self::$erron=1;
        self::$error=sprintf(self::$errorInfo[1], $filename);
        return false;
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
        $echo=sprintf('/%s(.+)%s/', self::$echoTag[0], self::$echoTag[1]);
        $comment=sprintf('/%s(.+)%s/', self::$commentTag[0], self::$commentTag[1]);
        return preg_replace(
            [$echo, $comment],
            ['<? Env::echo($1) ?>', '<?php /* $1 */ ?>'],
            $str
        );
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
