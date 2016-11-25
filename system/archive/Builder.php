<?php
namespace archive;

// 创建储存对象
class Builder
{

    public static function parser_str(string $sets)
    {
        $values=[];
        preg_match_all('/(\w+)(?:=(\'|")?(\S+)(?(2)\2))?\s*/',$sets,$matchs);
        for ($i=0;$i<count($matchs[0]);$i++){
            $name=$matchs[1][$i];
            $str=strcmp($matchs[2][$i],'"') && strcmp($matchs[2][$i],'\'');
            $value=$matchs[3][$i];
            if (preg_match('/^(true|false)$/i',$matchs[3][$i])) {
                $value=$matchs[3][$i]==='true';
            }
            else if (is_numeric($matchs[3][$i])){
                settype($value,'integer');
            }
            $values[$name]=$value;
        }
        return $values;
    }
}
