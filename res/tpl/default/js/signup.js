"use strick"
window.addEventListener('load', function () {
    var uname = false;
    var upass = false;
    var email = false;
    var verify = false;

    var username = document.getElementById('username');
    var u_notice = document.getElementById('n-username');
    var uemail = document.getElementById('email');
    var e_notice = document.getElementById('n-email');
    var submit = document.getElementById('signup');

    var pass = document.getElementById('password');
    var rpass = document.getElementById('rpassword');

    var n_password = document.getElementById('n-password');
    var n_rpassword = document.getElementById('n-rpassword');

    var d_verify = document.getElementById('verify');
    var n_verify = document.getElementById('n-verify');
    var img = document.getElementById('verify_code');
    var img_url = img.src;
    img.addEventListener('click', function () {
        this.src = img_url + '?t=' + (new Date()).getTime();
    });
    d_verify.addEventListener('blur', function () {
        var ver = new ajax();
        ver.post('/user/ajax').values({ type: 'verify', code: this.value }).ready(
            function (asw) {
                if (asw.return == true) {
                    n_verify.innerHTML = '<span style="color:green">验证成功~</span>';
                    verify = true;
                    d_verify.parentNode.style.display = 'none';
                }
                else {
                    n_verify.innerHTML = '<span style="color:red">验证码错误！</span>';
                    d_verify.value = '';
                    img.click();
                    d_verify.focus();
                }
            }
        );
    });
    function verfy_username(name) {
        return /^[^\^\~\\`\!\@\#\$\%\&\*\(\)\-\+\=\.\/\<\>\{\}\[\]\\\|\"\':\s\x3f]+$/.test(name);
    }
    username.addEventListener('blur', function () {
        if (this.value.length > 3) {
            if (verfy_username(this.value)) {
                var ask = new ajax();
                ask.post('/user/ajax').values({ type: 'checkuser', user: this.value }).ready(
                    function (get) {
                        if (get.return === false) {
                            u_notice.innerHTML = '<span style="color:green">该用户名可用~</span>';
                            uname = true;
                        }
                        else {
                            u_notice.innerHTML = '<span style="color:red">该用户名已存在！</span>';
                            username.focus();
                        }
                    }
                );
            }
            else {
                u_notice.innerHTML = '<span style="color:red">用户名不能包含特殊字符！</span>';
                username.focus();
            }
        }
        else if (this.value.length < 4) {
            u_notice.innerHTML = '<span style="color:red">用户名不能少于4个字符！</span>';
            username.focus();
        }
    });
    uemail.addEventListener('blur', function () {
        if (/^\S+?[@](\w+?\.)+\w+$/.test(this.value)) {
            ask = new ajax();
            ask.post('/user/ajax').values({ type: 'checkemail', email: this.value }).ready(
                function (asw) {
                    if (asw.return === false) {
                        e_notice.innerHTML = '<span style="color:green">该邮箱可用~</span>';
                        email = true;
                    }
                    else {
                        e_notice.innerHTML = '<span style="color:red">该邮箱已被注册</span>';
                    }
                }
            );
        } else {
            e_notice.innerHTML = '<span style="color:red">该邮箱非法</span>';
            this.focus();
        }
    });

    pass.addEventListener('blur', function () {
        if (this.value.length < 8) {
            n_password.innerHTML = '<span style="color:green">该密码长度太短，请大于8个字符~</span>';
            verify_ok = false;
            this.focus();
        }
        else {
            n_password.innerHTML = '<span style="color:green">该密码可用~</span>';
            upass = true;
        }
    });
    rpass.addEventListener('blur', function () {
        if (this.value !== pass.value) {
            n_rpassword.innerHTML = '<span style="color:green">密码与之前输入的密码不一致</span>';
            upass = false;
            this.focus();
        }
        else {
            n_rpassword.innerHTML = '<span style="color:green">密码一致</span>';
            upass = true;
        }
    });
    submit.addEventListener('click', function () {
        if (uname && upass && email && verify) {
            signup = new XMLHttpRequest;
            signup.open('POST', '/user/ajax');
            signup.addEventListener('readystatechange', function () {
                switch (signup.readyState) {
                    case 1: submit.innerHTML = "服务器连接已建立"; break;
                    case 2: submit.innerHTML = "请求已接收"; break;
                    case 3: submit.innerHTML = "请求处理中"; break;
                    case 4:
                        if (signup.status === 200) {
                            var json = JSON.parse(signup.responseText);
                            if (json['return'] === true) {
                                submit.innerHTML = "注册成功！";
                                location.href = json.jump || document.referrer || '/';
                            }
                            else {
                                submit.innerHTML = "注册失败，请重试！";
                            }
                        }
                        break;
                    default:
                        submit.innerHTML = "请求出错，请重试！";
                }
            });
            signup.setRequestHeader('Content-Type', 'application/json ; charset=UTF-8');
            signup.send(JSON.stringify({
                type: 'signup',
                user: username.value,
                passwd: pass.value,
                email: uemail.value,
                code: d_verify.value,
            }));
        }
    });
});