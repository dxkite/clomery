# 页面控制

```php
Page::visit('/{id}/{name}', function ($id, $name) {
        echo 'OK ==> ', $id, $name;
    })
    ->with('id', 'int')
    ->with('name', 'string');
```
与

```php
(new Page_Controller(function ($id, $name) {
        echo 'OK ==> ', $id, $name;
    }))-> url('/{id}/{name}')->with('id', 'int')->with('name', 'string');
```

等效