<?php


var_dump(Storage::exist('D:\参考源码\乌云知识库\[XSS神器]XssEncode chrome插件 - 0x_Jin.html'));
$file =Storage::get('D:\参考源码\乌云知识库\[XSS神器]XssEncode chrome插件 - 0x_Jin.html');
$hx='/<h(\d+)>(.*)<\/h\1>/';
$link='/<a\s+?href="(.+?)">(.+?)<\/a>/';
$img='/<img\s+src="(.+?)"\s+alt="(.+?)"\s*\/>/';
// 标题
$str=preg_replace_callback($hx,function ($match){
    $head='######';
    $str=substr($head,0,$match[1]);
    $str.=' '.$match[2]."\r\n";
    return $str;
},$file);
// 链接
$str=preg_replace($link,'[$2]($1)',$str);
// 图片
$str=preg_replace($img,'![$2]($1)',$str);
$str=preg_replace('/<strong>(.+?)<\/strong>/','**$1**',$str);
$str=preg_replace('/<pre><code>(.+?)<\/code><\/pre>/ims',"```\r\n$1\r\n```\r\n",$str);
$str=preg_replace('/<p>(.+?)<\/p>/ims',"$1\r\n\r\n",$str);

var_dump($str);
