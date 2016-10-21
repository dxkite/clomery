# Pomelo 网页渲染引擎使用说明
在项目中，页面渲染的时候默认使用了Pomelo渲染引擎,(类Smail语法)
其中，页面元素包含了三种输出语句控制符
## 控制符
### 基本输出控制
| 控制符      | 说明|
|-------------|------------------------------------------------------------------------------------------|
| {{ value }} | 直接输出value的值, `{{ func() }}` 会输出函数的返回值,所有的输出会被函数`htmlspecialchars`转义 |
| {{! html }} | 功能与上述标识一样，但是不会调用函数`htmlspecialchars`对其转义，用于输出HTML                  |
| {-- --}     | 模板注释，会生成PHP注释，不会出现在网页中                                                    |

>:notice: 以上输出控制在前面加`!`后会被忽略，即为转义，用于适配js模板;

### 逻辑控制符
| 控制符                | 说明                                       |
|-----------------------|-------------------------------------------|
| @if ( expression )    | if    --> `<?php if (expression) :?>`     |
| @else                 | else  --> `<?php else: ?>`                |
| @elseif (expression)  | elseif--> ` <?php elseif (expression): ?>`|
| @endif                | endif --> `<?php endif; ?>`               |
| @for (expressions)    | for   --> `<?php for(expressions): ?>`    |
| @endfor               | endfor--> `<?php endfor; ?>`              |
| @foreach(exp)         | foreach--> `<?php foreach( exp): ?>`      |
| @endforeach           | endforeach --> `<?php endforeach; ?>`     |
| @while(exp)           | while --> `<?php while(exp): ?>`          |
| @endwhile             | endwhile -->`<?php endwhile; ?>`          |

### 扩展控制符
| 控制符                | 说明                                          |
|----------------------|-----------------------------------------------|
| @include(name[,value])| 包含控制目前是从模板根目录开始搜索，未计入模板路径|
| @url(args)            |  会调用Page::url 方法，具体请看 `Page` 的说明  |
| @markdown(md)         | 使用内置的Markdown解析器解析markdown           |

## 页面赋值
1. 设置的变量获取值   
    在PHP中调用`Page::set(name,value)`会向模板中注入一个值，通过表达式 `$_Page->name` 可以获取到`name`的值`value`
2. 获取值时设置默认值  
    如果想在`name`的值为空时，输出一个默认值，只需要以`$_Page->name(default)`的方式调用，如果`name`为值，就会输出
`default`的值；
3. 格式化输出值    
    当以 `$_Page->name(default,arg1,arg2,...)`的方式调用时，会开启格式化输出功能，其中 `name` 或者 `default` 
    的字符串会作为模板，`argx`作为模板中的各个参数。
4. 使用默认值的情况    
    当 name的值为 NULL、未设置和是空字符串的时候会使用默认值。



--------------------------------------------
以上 2016-10-21 17:03:29