<?php
    $str=json_decode('"ux这\u202eわかぃまぃだDD"');
    var_dump($str);
    var_dump(preg_match('/^[\w\x{4e00}-\x{9aff}]{4,12}$/u', $str,$match));
    var_dump($match);