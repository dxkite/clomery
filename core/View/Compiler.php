<?php
// 编译器
interface View_Compiler
{
    // 编译样式
    public function setThemes(string $theme='default');
    public function getThemes();
    public static function viewPath(string $name);
    public static function tplRoot();
    // 编译单文件
    public function compileFile(string $file);
    // 编译文本
    public function compileText(string $file);
    // 错误报错
    public function error();
    public function erron();
}
