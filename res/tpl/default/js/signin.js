window.addEventListener('load', function () {
    var uname = false;
    var upass = false;
    var username = document.getElementById('username');
    var u_notice = document.getElementById('n-username');
    var pass = document.getElementById('password');
    var n_password = document.getElementById('n-password');
    var submit = document.getElementById('signin');
    function verfy_username(name) {
        return /^[^\^\~\\`\!\@\#\$\%\&\*\(\)\-\+\=\.\/\<\>\{\}\[\]\\\|\"\':\s\x3f]+$/.test(name);
    }
    username.addEventListener('blur', function () {
        if (this.value.length > 3) {
            if (verfy_username(this.value)) {
                ask = new ajax();
                ask.post('/user/ajax').values({ type: 'checkuser', user: this.value }).ready(
                    function (get) {
                        if (get.return === false) {
                            u_notice.innerHTML = '<span style="color:red">该用户名不存在！</span>';
                            username.focus();
                        }
                        else {
                            u_notice.innerHTML = '<span style="color:green">用户名验证通过</span>';
                            uname = true;
                        }
                    }
                );
            }
            else {
                u_notice.innerHTML = '<span style="color:red">用户名不能包含特殊字符！</span>';
                username.focus();
            }
        } else if (this.value.length < 4) {
            u_notice.innerHTML = '<span style="color:red">用户名不能少于4个字符！</span>';
            username.focus();
        }
    });

    pass.addEventListener('blur', function () {
        if (this.value.length < 8) {
            n_password.innerHTML = '<span style="color:green">该密码长度太短，请大于8个字符~</span>';
            this.focus();
        }
        else {
            n_password.innerHTML = '<span style="color:green">密码Ok</span>';
            upass = true;
        }
    });
    submit.addEventListener('click', function () {
        console.log('signin', uname, upass);
        if (uname && upass) {
            signin = new XMLHttpRequest;
            signin.open('POST', '/user/ajax');
            signin.addEventListener('readystatechange', function () {
                switch (signin.readyState) {
                    case 1: submit.innerHTML = "服务器连接已建立"; break;
                    case 2: submit.innerHTML = "请求已接收"; break;
                    case 3: submit.innerHTML = "请求处理中"; break;
                    case 4:
                        if (signin.status === 200) {
                            var json = JSON.parse(signin.responseText);
                            if (json['return'] === true) {
                                submit.innerHTML = "登陆成功";
                                location.href = json.jump || document.referrer || '/';
                            }
                            else {
                                switch (json.erron) {
                                    case 1:
                                        submit.innerHTML = "用户名不存在";
                                        break;
                                    case 2:
                                        submit.innerHTML = "密码错误，请重试！";
                                        break;
                                    case 3:
                                        submit.innerHTML = "系统错误，请重试！";
                                        break;
                                    default:
                                        submit.innerHTML = "未知错误";
                                }
                            }
                        }
                        break;
                    default:
                        submit.innerHTML = "登陆出错，请重试！";
                }
            });
            signin.setRequestHeader('Content-Type', 'application/json ; charset=UTF-8');
            signin.send(JSON.stringify({
                type: 'signin',
                user: username.value,
                passwd: pass.value,
            }));
        }
    });
});