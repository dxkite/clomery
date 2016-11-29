<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"
    />
    <title>Demo DxUI</title>
    <script src="/static/dxui.min.js"></script>

    <script id="login_form" type="application/DxTPL">
        <form id="login-form" > 
        <div><!--#login--><input name="username" class="form-input" type="text" value="DXkite" pattern="[A-z]{3}" /></div>
        <div><!--#passwd--><input name="password" class="form-input" type="password" value="DXkite"/></div>
        </form>
    </script>
</head>

<body>
    <span class="popwindow" style="cursor:pointer;">Click Me</span>
    <span class="popwindow" style="cursor:pointer;">Click Me</span>
    <span class="popwindow" style="cursor:pointer;">Click Me</span>
    <span class="popwindow" style="cursor:pointer;">Click Me</span>
</body>
<script>
DxUI.loadCss('/static/theme/dxui.min.css'); //加载自己要用的CSS
window.addEventListener('load', function () {
    window.$ = DxDOM;
    DxUI.Toast('窗口加载成功', 1000).show();
    DxTPL.config({tags:["<!--#","-->"]});

    var form = DxTPL.template('login_form', { login: '登陆', passwd: '密码' });
    var win = new DxUI.Window({ title: '登陆窗口', content: form, btn: [
        'close',
        {
            text:'登陆按钮',
            class:"btn btn--min",
            action:'/api/user/signin',
            'data-form':'login-form',
            type:'AjaxButton',
            on:[
                ['message', function (event) {
                    console.log('a new message!');
                    if (event.detail.json.name == 'signinSuccess') {
                        DxUI.Toast('登陆成功，窗口将在1S后关闭！', 1000).show();
                        win.close(1000);
                    }
                    else if (event.detail.json.name='signed') {
                        DxUI.Toast('用户已经登陆！', 1000).show();
                        win.close(1000);
                    }
                    else{
                        DxUI.Toast('登陆失败，请重试！', 1000).show();
                    }
                }
            ]] 
        },
        {
            text:'注册按钮',
            class:"btn btn--min",
            on:[
                ['click', function (event) {
                    DxUI.Toast('注册功能未完成！', 1000).show();
                }
            ]] 
        }]});

    win.addEventListener('close', function (event) {
        // 阻止关闭
        // event.preventDefault();
        console.log('close_window:' + this.index);
    });

    $('.popwindow').on('click', function () {
        win.open();
    });

});
</script>

</html>