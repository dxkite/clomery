<?php

class Markdown_Manager
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
    
    protected function uploadMarkdown(string $markdown, stdClass $config)
    {
        // TODO: 可忽略的作者
        $uid=Common_User::user2Id($config->author);
        if ($uid<=0) {
            $uid=Common_User::hasSignin()['uid'];
        }
        Upload::setUid($uid);
        $markdown=$this->archive->getFromName(self::parsePath($this->root.'/'.$markdown));
        // 上传链接中使用过的文件
        $markdown=preg_replace_callback('/\[.+?\]\((.+?)\)/', [$this, 'uploadUsedResource'], $markdown);
        // 上传图片文件
        $markdown=preg_replace_callback('/\!\[.+?\]\((.+?)\)/', [$this, 'uploadImgResource'], $markdown);
        // 上传文章  就算上传重复也更新了图片
        if (Blog_Article::updateExistHash(md5($this->archive->filename),$markdown) ==0){
            $aid =Blog_Article::insertNew($uid,
            $config->title,
            $config->remark, $markdown,
            $config->date,
            $config->keeptop,
            $config->reply,
            1, md5($this->archive->filename));
        }
        $aid=0;
        if ($aid>0) {
            $tags=preg_split('/\s*;\s*/', $config->tags);
            Blog_Tag::addTagsToArticle($aid, 0, $tags);
            return $aid;
        } else {
            return 0;
        }
    }
    
    protected function parseImgResource($matchs)
    {
        // 网络链接文件
        if (preg_match('/(http|https)/', $matchs[2])) {
            $this->urloutside[$matchs[1]]=$matchs[2];
        }
        return $matchs[0];
    }

    protected function uploadImgResource($matchs)
    {
        $path=self::parsePath($this->root.'/'.self::parsePath($matchs[1]));
        // 获取压缩包内部文件
        if ($content=$this->archive->getFromName($path)) {
            $id=Upload::uploadString($content, basename($path), pathinfo($path, PATHINFO_EXTENSION), 1);
            return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Page::url('upload_file', ['id'=>$id, 'name'=>basename($matchs[1])])).')', $matchs[0]);
        }
        // 允许从网络上下载URL需求
        elseif (in_array($matchs[1], $this->urlsave)) {
            $tmpname= microtime(true).'.tmp';
            Storage::download($matchs[1], $tmpname);
            $id=Upload::register(basename($matchs[1]), $tmpname, pathinfo($matchs[1], PATHINFO_EXTENSION), 1);
            return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Page::url('upload_file', ['id'=>$id, 'name'=>basename($matchs[1])])).')', $matchs[0]);
        }
        return $matchs[0];
    }

    protected function uploadUsedResource($matchs)
    {
        $path=self::parsePath($this->root.'/'.self::parsePath($matchs[1]));
        // 获取压缩包内部文件
        if ($content=$this->archive->getFromName($path)) {
            $id=Upload::uploadString($content, basename($path), pathinfo($path, PATHINFO_EXTENSION), 1);
            return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Page::url('upload_file', ['id'=>$id, 'name'=>basename($matchs[1])])).')', $matchs[0]);
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
