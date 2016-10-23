<?php

class Blog_MdManager
{
    public static $parser=null;
    public static $config='config.json';
    public $archive=null;
    public $root='';
    // 指定保存的URL文件对象
    public $urlsave=[];
    public $urloutside=[];
    public $error='';
    
    public function __construct()
    {
        self::$parser=new Markdown\Parser();
    }

    /**
     * @param array $urlsave
     */
    public function setUrlsave(array $urlsave)
    {
        $this->urlsave = $urlsave;
    }

    public function readZipMarkdown(string $filename)
    {
        $zip=new ZipArchive;
        $res = $zip->open($filename);
        
        if ($res === true) {
            $this->archive=$zip;
            if ($config=self::getZipConfigFile()) {
                $root=dirname($config);
                $config_file=$zip->getFromName($config);
                // 去行注释
                $config_file=preg_replace('/\/\/(.+)$/m', '', $config_file);
                // 去多行注释
                $config_file=preg_replace('/\/\*(.+)\*\/$/m', '', $config_file);
                // 解析配置
                $config_set=json_decode($config_file);
                $this->root=$root;
                return self::previewMarkdown($config_set->index, $config_set);
            } else {
                $this->error='un readable zip article format';
                return -2;
            }
            $zip->close();
        } else {
            $this->error='read zip field!';
            return -1;
        }
    }
    public function uploadInfo()
    {
        return ['error'=>$this->error];
    }
    public function MdChange(string $preg, string $replace, bool $console=false)
    {
        // 控制台日志
        $log=function ($values) use ($console) {
            if ($console) {
                foreach (func_get_args() as $value) {
                    print_r($value);
                    print "\r\n";
                }
            }
        };

        $q =new Query('SELECT `aid`,`contents` FROM `atd_articles`');
        while ($get=$q->fetch()) {
            $log('read article:'.$get['aid']);
            $markdown=preg_replace_callback('/\!\[(.+?)\]\((.+?)\)/', function ($matchs) use ($log, $preg, $replace) {
                $result=preg_replace($preg, $replace, $matchs[2]);
                $log($preg, $replace, $matchs[2], $result);
                $log($matchs[0].' --> !['.$matchs[1].']('.$result.')');
                return '!['.$matchs[1].']('.$result.')';
            }, $get['contents']);
            $count=(new Query('UPDATE `atd_articles` SET `contents`=:contents  WHERE `aid`=:aid', ['aid'=>$get['aid'], 'contents'=>$markdown]))->exec();
            $log('change article:'.$count);
        }
    }

    public function uploadZipMarkdown(string $filename, string $name='')
    {
        $zip=new ZipArchive;
        $res = $zip->open($filename);
        
        if ($res === true) {
            $this->archive=$zip;
            if ($config=self::getZipConfigFile($name)) {
                $root=dirname($config);
                $config_file=$zip->getFromName($config);
                // 去行注释
                $config_file=preg_replace('/\/\/(.+)$/m', '', $config_file);
                // 去多行注释
                $config_file=preg_replace('/\/\*(.+)\*\/$/m', '', $config_file);
                // 解析配置
                $config_set=json_decode($config_file);
                $this->root=$root;
                return self::uploadMarkdown($config_set->index, $config_set);
            } else {
                $this->error='un readable zip article format';
                return -2;
            }
            $zip->close();
        } else {
            $this->error='read zip failed!';
            return -1;
        }
    }
    protected function parserSetting()
    {
    }
    protected function previewMarkdown(string $markdown,  stdClass $config)
    {
        $markdown=$this->archive->getFromName(self::parsePath($this->root.'/'.$markdown));
        // 上传图片文件
        $markdown=preg_replace_callback('/\!\[(.+?)\]\((.+?)\)/', [$this, 'parseImgResource'], $markdown);
        $mkhtml=self::$parser->makeHTML($markdown);
        var_dump($this->urloutside);
    }
    protected function parseString(string $title)
    {
        return preg_replace('/\s+?/', '-', $title);
    }
    protected function uploadMarkdown(string $markdown, stdClass $config)
    {
        $uid=-1;
        // TODO: 可忽略的作者
        if (isset($config->author)) {
            $uid=Common_User::user2Id($config->author);
        }
        // 未指定作者
        if ($uid<0) {
            $uid=Common_User::hasSignin()['uid'];
        }

        Upload::setUid($uid);
        
        // 获取文章内容
        $markdown=$this->archive->getFromName(self::parsePath($this->root.'/'.$markdown));
        // 上传链接中使用过的文件 (已经包含了图片文件)
        $markdown=preg_replace_callback('/\[.+?\]\((.+?)\)/', [$this, 'uploadUsedResource'], $markdown);
        // 上传图片文件
        // $markdown=preg_replace_callback('/\!\[.+?\]\((.+?)\)/', [$this, 'uploadImgResource'], $markdown);
        // 设置AID
        $aid=isset($config->id)?$config->id:0;
        // 如果文章ID存在，更新文章内容
        if (isset($config->id) && Blog_Article::updateExistId(
                    $config->id,
                    $uid,
                    self::parseString($config->title),
                    $config->remark, $markdown,
                    isset($config->date)?$config->date:time(),
                    $config->keeptop,
                    $config->reply,
                    isset($config->public)?$config->public:1,
                    md5($this->archive->filename))) {
        }
        // ID不存在 相同文件md5则更新文件资源
        // 否则就作为新文章上传
        elseif (Blog_Article::updateExistHash(md5($this->archive->filename), $markdown, isset($config->date)?$config->date:time()) ==0) {
            $aid =Blog_Article::insertNew($uid,
            self::parseString($config->title),
            $config->remark, $markdown,
            isset($config->date)?$config->date:time(),
            $config->keeptop,
            $config->reply,
            1, md5($this->archive->filename));
        }
        if ($aid>0) {
            // 设置标签
            if (isset($config->tags)) {
                Blog_Tag::setTagsToArticle($aid, 0, Blog_Tag::tags2Array($config->tags));
            }
            // 设置分类
            if (isset($config->category_id)) {
                var_dump('set Category='.Blog_Category::setCategory($aid, $config->category_id));
            } elseif (isset($config->category)) {
                $categorys=preg_split('/\s*(-)\s*/', $config->category);
                var_dump('cid='.Blog_Category::getCategoryId(end($categorys)));
                var_dump('set Category='.Blog_Category::setCategory($aid, Blog_Category::getCategoryId(end($categorys))));
            }
        }
        return $aid;
    }
    
    protected function parseImgResource($matchs)
    {
        // 网络链接文件
        if (preg_match('/(http|https)/', $matchs[2])) {
            $this->urloutside[$matchs[1]]=$matchs[2];
        }
        return $matchs[0];
    }

    // protected function uploadImgResource($matchs)
    // {
    //     $path=self::parsePath($this->root.'/'.self::parsePath($matchs[1]));
    //     // 获取压缩包内部文件
    //     if ($content=$this->archive->getFromName($path)) {
    //         $id=Upload::uploadString($content, basename($path), pathinfo($path, PATHINFO_EXTENSION), 1);
    //         return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Upload::url($id, basename($matchs[1]))).')', $matchs[0]);
    //     }
    //     // 允许从网络上下载URL需求
    //     elseif (in_array($matchs[1], $this->urlsave)) {
    //         $tmpname= microtime(true).'.tmp';
    //         Storage::download($matchs[1], $tmpname);
    //         $id=Upload::register(basename($matchs[1]), $tmpname, pathinfo($matchs[1], PATHINFO_EXTENSION), 1);
    //         return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Upload::url($id, basename($matchs[1]))).')', $matchs[0]);
    //     }
    //     return $matchs[0];
    // }

    protected function uploadUsedResource($matchs)
    {
        $path=self::parsePath($this->root.'/'.self::parsePath($matchs[1]));
        // 获取压缩包内部文件
        if ($content=$this->archive->getFromName($path)) {
            $id=Upload::uploadString($content, basename($path), pathinfo($path, PATHINFO_EXTENSION), 1);
            return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Upload::url($id, basename($matchs[1]))).')', $matchs[0]);
        }
        // 允许从网络上下载URL需求
        elseif (in_array($matchs[1], $this->urlsave)) {
            $tmpname= microtime(true).'.tmp';
            Storage::download($matchs[1], $tmpname);
            $id=Upload::register(basename($matchs[1]), $tmpname, pathinfo($matchs[1], PATHINFO_EXTENSION), 1);
            return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Upload::url($id, basename($matchs[1]))).')', $matchs[0]);
        }
        return $matchs[0];
    }
    
    // 获取 配置
    protected function getZipConfigFile(string $name='')
    {
        if (!$name) {
            $name=pathinfo(basename($this->archive->filename), PATHINFO_FILENAME);
        }
        $root_dir = $name;
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
