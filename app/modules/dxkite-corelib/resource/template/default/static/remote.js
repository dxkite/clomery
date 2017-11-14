; /*! remote call */
(function (win) {
    // 全局严格模式
    "use strict";
    var apibase = '/api';
    var call_id = 0;

    var that_dom = function () {
        var scripts = document.getElementsByTagName("script");
        return scripts[scripts.length - 1];
    }();

    apibase = that_dom.dataset.api || apibase;

    function objectHasFile(args) {
        for (var index in args) {
            if (args[index] instanceof File || args[index] instanceof Blob) {
                return true;
            }
        }
        return false;
    }

    function Caller(obj) {
        call_id++;
        this.callback = {};
        this.obj = obj;
        this.call = function (args) {
            var ajax = new XMLHttpRequest;
            var that = this;

            var isObjectHasFile = arguments.length === 1 && objectHasFile(arguments);

            ajax.addEventListener("readystatechange", function () {
                if (ajax.readyState == 4) {
                    if (ajax.status == 200) {
                        var json = JSON.parse(ajax.responseText);
                        // console.log("remote call "+obj.class+"."+obj.method,json);
                        if (typeof json.result !== 'undefined') {
                            that.callback.result.call(that, json.result);
                        } else {
                            if (that.callback.error) {
                                that.callback.error.call(that, json.error);
                            } else {
                                console.error(json.error.name + ":" + json.error.message, json);
                            }
                        }
                    } else {
                        if (that.callback.fail) {
                            that.callback.fail.call(that);
                        }
                    }
                }
            });

            var url = obj.class === null ? obj.apibase : /^https?:\/\//.test(obj.class) ? obj.class : obj.apibase + '/' + obj.class;

            if (isObjectHasFile || args instanceof FormData) {
                ajax.open("POST", url + '?method=' + obj.method);
            } else {
                ajax.open("POST", url);
                ajax.setRequestHeader("Content-Type", "application/json");
            }
            // 提交表单
            if (args instanceof FormData) {
                ajax.send(args);
            } else if (isObjectHasFile) { // 含有文本
                var form = new FormData();
                for (var name in args) {
                    form.append(name, args[name]);
                }
                ajax.send(form);
            } else {
                // 数组
                var params = new Array();
                for (var i = 0; i < arguments.length; i++) {
                    params.push(arguments[i]);
                }
                ajax.send(JSON.stringify({
                    method: obj.method,
                    params: params,
                    id: call_id,
                }));
            }
            return this;
        }

        this.result = function (callback) {
            this.callback.result = callback;
            return this;
        }
        this.fail = function (callback) {
            this.callback.fail = callback;
            return this;
        }
        this.error = function (callback) {
            this.callback.error = callback;
            return this;
        }
        return this;
    }

    function CallMethod(classname, method, base) {
        if (typeof method === 'undefined') {
            this.class = null;
            this.method = classname;
        } else {
            this.class = classname;
            this.method = method;
        }
        this.apibase = base || apibase;
        return new Caller(this);
    }

    window.remote = function (classname, method, base) {
        return new CallMethod(classname, method, base || apibase);
    }
    window.CallMethod = CallMethod;
})(window);