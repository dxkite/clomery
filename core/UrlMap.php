<?php
class UrlMap
{
    public $maps;
    public function load(string $file)
    {
        $read=file($file);
        
        foreach ($read as $line) {
        
            if (preg_match('/^\s*(?!;)(\w+)\s+(\S+?)\s+(?:([^[]+)(\S+)?)\s+(.*)/', $line, $duri)) {
                var_dump(new Dyuri($duri));
            }
        }
    }
}
