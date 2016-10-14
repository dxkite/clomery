<?php

class Markdown_Manager
{
    public static $parser=null;
    public static $config='config.json';
    public $archive=null;
    public $root='';

    public function __construct()
    {
        self::$parser=new Markdown\Parser();
    }
    public function parseFile()
    {
    }
    public function readZip(string $filename)
    {
        $zip=new ZipArchive;
        $res = $zip->open($filename);
        
        if ($res === true) {
            $this->archive=$zip;
            if ($config=self::getZipConfigFile()) {
                $root=dirname($config);
                $config_file=$zip->getFromName($config);
                // 去行注释
                $config_file=preg_replace('/^(\s*?)[\/][\/](.+)$/m', '', $config_file);
                $config_set=json_decode($config_file);
                $this->root=$root;
                $hello=self::getMarkdown($config_set->article, $config_set);
                var_dump($hello);
            } else {
                echo 'un readable zip format';
            }
            $zip->close();
        } else {
            echo 'failed';
        }
    }

    protected function getMarkdown(string $markdown, stdClass $config)
    {
        var_dump($this->root.'/'.$markdown);
        $markdown=$this->archive->getFromName($this->root.'/'.$markdown);
        $markdown=preg_replace_callback('/\[.+?\]\((.+?)\)/', [$this, 'parseResource'], $markdown);
        var_dump($markdown);
        var_dump(self::$parser->makeHTML($markdown));
    }

    protected function parseResource($matchs)
    {
        // 获取压缩包内部文件
        if ($content=$this->archive->getFromName($path=$this->root.'/'.self::parsePath($matchs[1]))) {
            $id=Upload::uploadString($content, basename($path), pathinfo($path, PATHINFO_EXTENSION),1);
            return  preg_replace('/\((.+?)\)$/','('.str_replace('$','\$',Page::url('upload_file',['id'=>$id,'name'=>basename($matchs[1])])).')',$matchs[0]);
        }
        else if (preg_match('//')) {

        }
        return $matchs[0];
    }
    // 获取 配置
    protected function getZipConfigFile()
    {
        $root_dir = pathinfo(basename($this->archive->filename), PATHINFO_FILENAME);
        if ($statconfig=$this->archive->statName(self::$config)) {
            if ($statconfig['comp_method']===8) {
                return self::$config;
            }
        } else {
            for ($i = 0; $i < $this->archive->numFiles; $i++) {
                $filename = $this->archive->getNameIndex($i);
                $stat=$this->archive->statName($filename);
                if (strcmp($filename, $root_dir .'/')==0 && $stat['comp_method']===0) {
                    if ($statconfig=$this->archive->statName($filename.self::$config)) {
                        if ($statconfig['comp_method']===8) {
                            return $filename.self::$config;
                        }
                    }
                }
            }
        }
        return false;
    }
    protected function parsePath($path)
    {
        // 根目录去除
        $path=preg_replace('/^(\.{1,2}(\/|\\\\))*/', '', $path);
        // 回溯 xxx/abc/../ --> /
        $preg='/(\/|\\\\)(.+?)(\/|\\\\)\.\./';
        while (preg_match($preg, $path)) {
            $path=preg_replace($preg, '', $path);
        }
        $path=preg_replace('/^(.+?)(\/|\\\\)\.\.(\/|\\\\)/', '', $path);
        return $path;
    }
}
