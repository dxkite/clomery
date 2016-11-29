<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"
    />
    <title>Demo DxUI</title>
    <script src="/static/dxui.js"></script>

    <script id="login_form" type="application/DxTPL">
        <form id="login-form" > 
        <div><!--#login--><input name="name" class="form-input" type="text" value="DXkite" /></div>
        <!--#if email  -->
        <div><!--#email--><input name="email" class="form-input" type="email" value="DXkite@atd3.com" /></div>
        <!--#/if-->
        <div><!--#passwd--><input name="password" class="form-input" type="password" value="DXkite"/></div>
        </form>
    </script>
</head>

<body>
    <?php var_dump(User::getSignInUserId()) ?>
    <span class="signin" style="cursor:pointer;">登陆</span>
    <span class="signup" style="cursor:pointer;">注册</span>
</body>
<script>
DxUI.loadCss('/static/theme/dxui.css'); //加载自己要用的CSS
window.addEventListener('load', function () {
    window.$ = DxDOM;
    DxUI.Toast('窗口加载成功', 1000).show();
    DxTPL.config({tags:["<!--#","-->"]});
    var form = DxTPL.template('login_form', { login: '登陆',email:false, passwd: '密码' });
    var form2 = DxTPL.template('login_form', { login: '登陆',email:'邮箱', passwd: '密码' });

    var win2= new DxUI.Window({ title: '注册窗口', content: form2, btn: [
        'close',
        {
            text:'注册',
            class:"btn btn--min",
            action:'/user/ajax?type=signup',
            'data-form':'login-form',
            type:'AjaxButton',
            on:[
                ['message', function (event) {
                   console.log(event.detail.json);
                }
            ]] 
        }]});



    var win = new DxUI.Window({ title: '登陆窗口', content: form, btn: [
        'close',
        {
            text:'登陆按钮',
            class:"btn btn--min",
            action:'/user/ajax?type=signin',
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
        }]});



    $('.signin').on('click', function () {
        win.open();
    });

     $('.signup').on('click', function () {
        win2.open();
    });

});
</script>

</html>