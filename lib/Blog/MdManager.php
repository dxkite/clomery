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
    public $attachment=[];

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
        $ret=[];
        if ($res === true) {
            $this->archive=$zip;
            $file=self::getMdFiles();
            $ret=['file'=>$file,'return'=>self::uploadMarkdown($file)];
            $zip->close();
        } else {
            $this->error='read zip failed!';
            return -1;
        }
        return $ret;
    }

    protected function getMdFiles()
    {
        for ($i = 0; $i < $this->archive->numFiles; $i++) {
            $stat=$this->archive->statIndex($i);
            if ($stat['comp_method']===8 && preg_match('/(readme|index)\.md/i', $stat['name']) === 1) {
                return  $stat['name'];
            }
        }
    }

    protected function previewMarkdown(string $markdown,  stdClass $config)
    {
        $markdown=$this->archive->getFromName(self::parsePath($this->root.'/'.$markdown));
        // 上传图片文件
        $markdown=preg_replace_callback('/\!\[(.+?)\]\((.+?)\)/', [$this, 'parseImgResource'], $markdown);
        $mkhtml=self::$parser->makeHTML($markdown);
        var_dump($this->urloutside);
    }
    
    public function testmdh(string $title)
    {
        return self::parseMdHead(Storage::get($title));
    }
    
    protected function parseMdHead(string $markdown)
    {
        $config=null;
        $config['time']=time();
        $config['public']=1;
        $config['top']=0;
        $config['reply']=1;
        $markdown=preg_replace_callback('/^\s*(#(?:.+?))-{3,}\r?\n/ism', function ($matchs) use (&$config) {
            $header=$matchs[1];
            // 我是不是该用下循环？？
            if (preg_match('/^\s*#{1,6}\s*(.+)$/im', $header, $tagmatch)) {
                $config['title']=$tagmatch[1];
            }
            if (preg_match('/^\s*(tags?|标签)([^:]*?)\s*:\s*(.+)$/im', $header, $tagmatch)) {
                $config['tags']=$tagmatch[3];
            }
            if (preg_match('/^\s*(a?id|修改)([^:]*?)\s*:\s*(.+)$/im', $header, $tagmatch)) {
                $config['aid']=(int)$tagmatch[3];
            }
            if (preg_match('/^\s*(categorys?|分类)([^:]*?)\s*:\s*(.+)$/im', $header, $tagmatch)) {
                $config['categorys']=$tagmatch[3];
            }
            if (preg_match('/^\s*(author|作者)([^:]*?)\s*:\s*(.+)$/im', $header, $tagmatch)) {
                $config['author']=$tagmatch[3];
            }
            if (preg_match('/^\s*(times?|时间)([^:]*?)\s*:\s*(.+)$/im', $header, $tagmatch)) {
                if (($unix=strtotime($tagmatch[3])) > 0) {
                    $config['time']=$unix;
                }
            }
            if (preg_match('/^\s*(remark|摘要)([^:]*?)\s*:\s*(.+)\r?\n\r?\n/ims', $header, $tagmatch)) {
                $config['remark']=$tagmatch[3];
            }
            if (preg_match('/^\s*(status?|状态)([^:]*?)\s*:\s*(.+)\r?\n\r?\n/ims', $header, $tagmatch)) {
                if (preg_match('/(save|草稿)/i', $tagmatch[3])) {
                    $config['public']=0;
                }
                if (preg_match('/((keep)?top|置顶)/i', $tagmatch[3])) {
                    $config['top']=1;
                }
                if (preg_match('/(noreply|(不允许)?回复)/i', $tagmatch[3])) {
                    $config['reply']=0;
                }
            }
        }, $markdown, 1);
        return  ['config'=>$config,'markdown'=>$markdown];
    }
    
    protected function uploadMarkdown(string $filename)
    {
        // 获取文章内容
        $markdown=$this->archive->getFromName($filename);
        // 从文章头中获取Markdown
        $parse=self::parseMdHead($markdown);
        $markdown=$parse['markdown'];
        $config=$parse['config'];
        
        if (!$config) {
            return -1;
        }

        $uid=Common_User::hasSignin()['uid'];
        // 设置了作者
        if (isset($config['author'])) {
            $user=Common_User::user2Id($config['author']);
            // 查看是否有代替他人的权限
            if ($user != $uid && Common_Auth::su2Other($uid)) {
                $uid =$user;
            }
        }
        Upload::setUid($uid);
        // var_dump($config);
        // var_dump('User='.$uid);
        // 设置Markdown的相对根目录
        $this->root=dirname($filename);
        // 上传链接中使用过的文件
        $markdown=preg_replace_callback('/\[.+?\]\((.+?)\)/', [$this, 'uploadUsedResource'], $markdown);
        //  标记文件
        $aid=isset($config['aid'])?$config['aid']:-1;
        if (!isset($config['remark'])) {
            if (preg_match('/^(.+?)\r?\n\r?\n/ims', $markdown, $match_remark)) {
                $remark=$match_remark[1];
            }
            // 如果字符长度大于255，删除最后一段
            if (strlen($remark)>255) {
                $remark=preg_replace('/\r?\n(.+)\r?\n\r?\n$/ims', '', $remark);
            }
        }
        
        // 如果文章ID存在，更新文章内容
        if (isset($config['aid']) && Blog_Article::updateExistId(
                    $config['aid'],
                    $uid,
                    $config['title'],
                    $remark, $markdown,
                    $config['time'],
                    $config['top'],
                    $config['reply'],
                    $config['public'],
                    md5($markdown+time()))) {
            var_dump('exsits');
        }
        // ID不存在 相同文件md5则更新文件资源
        // 否则就作为新文章上传
        elseif (Blog_Article::updateExistHash(md5($markdown), $markdown, $config['time']) ==0) {
            $aid =Blog_Article::insertNew($uid,
            $config['title'],
            $remark, $markdown,
            $config['time'],
            $config['top'],
            $config['reply'],
            $config['public'],
            md5($markdown));
        }
        if ($aid>0) {
            self::markdownSetFor($aid);
            // 设置标签
            if (isset($config['tags'])) {
                var_dump('set tags='.Blog_Tag::setTagsToArticle($aid, 0, Blog_Tag::toArray($config['tags']))) ;
            }
        
            // 设置分类
            if (isset($config['categorys'])) {
                if (Common_Auth::editCategory($uid)){
                    var_dump('create categorys='.Blog_Category::createCategorys($config['categorys']));
                }
                var_dump('set Category='.Blog_Category::setCategory($aid, $config['categorys']));
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

    protected function uploadUsedResource($matchs)
    {
        $path=self::parsePath($this->root.'/'.self::parsePath($matchs[1]));
        // 获取压缩包内部文件
        if ($content=$this->archive->getFromName($path)) {
            $id=Upload::uploadString($content, basename($path), pathinfo($path, PATHINFO_EXTENSION), 1);
            var_dump('Upload='.$id);
            $this->attachment[]=$id;
            return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Upload::url($id, basename($matchs[1]))).')', $matchs[0]);
        }
        // 允许从网络上下载URL需求
        elseif (in_array($matchs[1], $this->urlsave)) {
            $tmpname= microtime(true).'.tmp';
            Storage::download($matchs[1], $tmpname);
            $id=Upload::register(basename($matchs[1]), $tmpname, pathinfo($matchs[1], PATHINFO_EXTENSION), 1);
            $this->attachment[]=$id;
            return  preg_replace('/\((.+?)\)$/', '('.str_replace('$', '\$', Upload::url($id, basename($matchs[1]))).')', $matchs[0]);
        }
        return $matchs[0];
    }

    protected function markdownSetFor(int $for)
    {
        foreach ($this->attachment as $id) {
            Upload::setForWhat($id, $for, 0);
        }
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
