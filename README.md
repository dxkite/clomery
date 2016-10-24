# 多功能混合网站(CMS?)
网站功能主要分四大功能：    
- [ ] Blog      
    基本功能：在线Markdown博客
- [ ] Community     
    基本功能：在线Markdown社区
- [ ] Online Judge      
    扩展功能：在线测评（后续考虑添加）
- [ ] Video Online Play     
    扩展功能：视频在线播放

## 辅助系统 
### XCore

- [x] View 模板引擎
- [x] Qurey 数据库引擎
- [x] Storage 文件引擎
- [x] Session 辅助
- [x] Cookie 辅助
- [x] Cache 缓存引擎
- [x] WebSocket 辅助
- [ ] Socket 辅助
- [x] Database 辅助
- [ ] Mail 邮件引擎
    - [x] sendmail 辅助引擎
    - [ ] STMP 邮件辅助引擎
- [ ] Debug工具
- [ ] Cmd命令工具
- [ ] 扩展库
    - [x] 验证码生成器
    - [ ] Markdown编写工具

### js

- [ ] Dxui UI库
- [ ] DxDOM DOM操纵辅助

## 网站功能实现细则
- [ ] v1.0 
    - [x] 用户功能 - 注册/登陆/注销/删除
    - [ ] 后台管理 - 用户管理
    - [ ] 反馈功能 - 留言版     
- [ ] v1.1 
    - [x] 文章功能 - 上传zip文章 
    - [x] 通过分类查看文章
    - [x] 通过标签查看文章   

  
## 使用说明
.conf.semple 为配置文件模板
配置好后改名为 .conf 放到 /res 目录下  
res 目录要保证服务器可读写     
运行脚本 Install.php
```
php Install.php
```
## MIT

------------------------------------------------
唉，中文的URL总是各种错误，没法用啊！！！！后续还要改点东西~！wtf
### Windows不支持url: 
- 403 在第一个URL中使用`:`   
    会被认为是类似`domain.com/盘符:/xx`判断为不安全路径（Apache Bug [Bug 41441 - Error 20024 on all pages request containing a ":"](https://bz.apache.org/bugzilla/show_bug.cgi?id=41441#c3) )|
- 403 在第一个URL中使用 `%85`->`0x85`   
- 404 奇怪编码组合 `%85%E9`->`0x85+0xe9`    
    原因未知

------------------------------------------------
我放弃统计了，基本上一个字节，两个字节的错误非常多。
错误为 400，403，404 回头加个windows修正模块
**ErrorDocument 定向失败** Windows 不支持URL重写