<?php /// 访问规则
 Page::visitController((new Page_Controller(function ($id, $name) {
     echo 'OK ==> ', $id, $name;
 }))-> url('/{id}/{name}')->with('id', 'int')->with('name', 'string'));
    
 Page::visit('/getUser/{id}', function ($id=0) {
     var_dump(Page::url('main', ['id'=>5, 'name'=>'urlpage']));
     return (new Qurey('SELECT * FROM `#{users}` WHERE `uid`=:uid LIMIT 1;', ['uid'=>$id]))->fetch();
 })
    ->with('id', 'int')
    ->json();

  Page::default(function ($page) {
      View::set('hello', '404 - No  Find');
      echo '__default__';
  });
 Page::auto('/admin', ['/admin']);
