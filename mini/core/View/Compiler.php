<?php
// 编译器
interface View_Compiler
{
    // 编译单文件
    public function compileFile(string $file, array $include_path=[]);
    // 编译多文件
    public function compileFiles(array $files, array $include_path=[]);
    // 编译文本
    public function compileText(string $file, array $include_path=[]);
    // 错误报错
    public function error();
    public function erron();
}
