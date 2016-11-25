<?php
namespace template;

use Storage;

class Builder
{
    public static $extRaw='.tpl.html';
    public static $extCpl='.tpl';
    protected static $errorInfo=[
        0=>'No Error',
        1=>'File %s Not Exist',
        2=>'Compile Include %s Error',
    ];
    protected static $error='';
    protected static $erron=0;
    protected static $rawTag=['{{!','}}'];
    protected static $echoTag=['{{','}}'];
    protected static $commentTag=['{--','--}'];
   
    protected static $theme='default';

    public static function viewPath(string $name)
    {
        $file=preg_replace('/[.|\\\\|\/]+/', DIRECTORY_SEPARATOR, $name);
        $path= SITE_VIEW.'/'.$file.self::$extCpl;
        return $path;
    }
    // 编译单文件
    public function compileFile(string $filename)
    {
        $file=preg_replace('/^'.preg_quote(SITE_TEMPLATE.'/'.self::$theme.'/', '/').'/', '', $filename);
        $output=SITE_VIEW.'/'.preg_replace('/'.preg_quote(self::$extRaw).'$/', self::$extCpl, $file);
        if (Storage::exist($filename)) {
            $content= self::compileText(Storage::get($filename));
            if (!Storage::isDir($dir=dirname($output))) {
                Storage::mkdirs(dirname($output));
            }
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
        $filename=SITE_TEMPLATE.'/'.self::$theme.'/'.$file.self::$extRaw;
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
    public function setTheme(string $theme='default')
    {
        self::$theme=$theme;
    }
    public function getTheme()
    {
        return self::$theme;
    }
    public function tplRoot()
    {
        return SITE_TEMPLATE.'/'.self::$theme;
    }



    private function compileString(string $str)
    {
        $callback=function ($match) {
            // var_dump($match);
            if (method_exists($this, $method = 'parse'.ucfirst($match[1]))) {
                $match[0] = $this->$method(isset($match[3])?$match[3]:null);
            }
            /*else {
                $match[0] ='<?php  Pomelo::'.ucfirst($match[1]).$match[3].' ?>';
            }*/
            return isset($match[3]) ? $match[0] : $match[0].$match[2];
        };
        return preg_replace_callback('/\B@(\w+)(\s*)(\( ( (?>[^()]+) | (?3) )* \) )? /x', $callback, $str);
    }

    private function compileCommand(string $str)
    {
        $echo=sprintf('/(?<!!)%s\s*(.+?)\s*?%s/', preg_quote(self::$echoTag[0]), preg_quote(self::$echoTag[1]));
        $rawecho=sprintf('/(?<!!)%s\s*(.+?)\s*?%s/', preg_quote(self::$rawTag[0]), preg_quote(self::$rawTag[1]));
        $comment=sprintf('/(?<!!)%s(.+)%s/', preg_quote(self::$commentTag[0]), preg_quote(self::$commentTag[1]));
        return preg_replace(
            [$rawecho, $echo, $comment],
            ['<?php echo($1) ?>', '<?php template\Builder::echo($1) ?>', '<?php /* $1 */ ?>'],
            $str
        );
    }
    protected function parseEcho($exp)
    {
        return "<?php template\Builder::echo{$exp} ?>";
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
        return "<?php Page::render{$exp} ?>";
    }

    // View echo
    public static function echo($something)
    {
        foreach (func_get_args() as $arg) {
            echo htmlspecialchars($arg);
        }
    }


    protected function parseInsertAt($exp)
    {
        preg_match('/\((.+)\)/', $exp, $v);
        return '<?php Event::listen('.$v[1].',function () { ?>';
    }
    protected function parseInsertEnd()
    {
        return '<?php });?>';
    }
    protected function parseInsert($exp)
    {
        return "<?php Event::pop{$exp}->exec() ?>";
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
