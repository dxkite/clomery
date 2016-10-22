<?php
$values=[
    'title'=>'DxCore',
    'message'=>'hello world',
];
Page::global('_Mail',new Core\Value($values));
Page::use('__mail__/mail');