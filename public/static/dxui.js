!function() {
    "use strict";
    var DxTPL = {};
    //  缓存查找节点可能会耗时较多 
    var defaults = {
        cache: true,    // 是否开启缓存
        tags: ['{', '}'], //控制标签
        compress: true,
        use_strict: true,
    };
    // 关键字
    var keywords = 'if,else,each,include,while,for';
    var keyword_preg = '^\\s*((?:\/)?(?:' + keywords.split(',').join('|') + '))(.*)';

    var tagstart = defaults.tags[0];
    var tagend = defaults.tags[1];
    var cache = defaults.cache;
    var compress = defaults.compress;
    var use_strict = defaults.use_strict;
    DxTPL.config = function(config) {
        cache = (typeof config.cache !== undefined) ? config.cache : defaults.cache;
        compress = (typeof config.compress !== undefined) ? config.compress : defaults.compress;
        if (config.tags && config.tags.length === 2) {
            tagstart = config.tags[0];
            tagend = config.tags[1]
        }
    }
    // @artTemplate:https://github.com/aui/artTemplate
    var is_new_engine = ''.trim;
    var replaces = is_new_engine
        ? ["$_tpl_=''", "$_tpl_+=", ";", "$_tpl_"]
        : ["$_tpl_=[]", "$_tpl_.push(", ");", "$_tpl_.join('')"];

    var escape = {
        "<": "&#60;",
        ">": "&#62;",
        '"': "&#34;",
        "'": "&#39;",
        "&": "&#38;"
    };
    var statment_test = function(test, code) {
        try {
            new Function(test);
        } catch (e) {
            return 'throw ' + e.name + '(' + _string(e.message) + ');{';
        }
        return code;
    }
   /**
     * 复制合并对象
     * 
     * @param {Object|string} arrays
     * @returns
     */
    function object_copy(arrays) {
        var object = {};
        for (var i = 0; i < arguments.length; i++) {
            for (var index in arguments[i]) {
                object[index] = arguments[i][index];
            }
        }
        return object;
    }
    var parsers = {
        html: function(html) {
            // console.log('HTML:', html);
            var out = '';
            if (html.match(/(?!^)\n/)) {
                _each(html.split('\n'), function(html) {
                    if (html) {
                        // 压缩多余空白与注释
                        if (compress) {
                            html = html.replace(/\s+/g, ' ').replace(/<!--.*?-->/g, '');
                        }
                        if (html) {
                            out += replaces[1] + _string(html) + replaces[2];
                            out += '\n';
                        }
                    }
                });
            } else if (html) {
                out += replaces[1] + _string(html) + replaces[2];
            }
            return out;
        },
        code: function(code) {
            var match;
            // console.log(new RegExp(keyword_preg));
            if (match = code.match(new RegExp(keyword_preg))) {
                // console.log(code,':',match);
                var command = match[1];
                var param = match[2];

                switch (command) {
                    case 'include': // 编译时包含
                        param = param.trim().split(' ');
                        if (param.length === 1) {
                            param.push("$_unit.value");
                        }
                        param = param.join(',');
                        return replaces[1] + '$_unit._include(' + param + ')' + replaces[2];
                    case 'if':
                        return statment_test('if(' + param + '){}', 'if (' + param + ') {');
                    case 'else':
                        // console.log(param,param.match(/^\s*if\s+(.*)/));
                        if (match = param.match(/^\s*if\s+(.*)/)) {
                            return '} else if (' + match[1] + '){';
                        }
                        return '}else{';
                    case '/if':
                    case '/while':
                    case '/for':
                        return '}';
                    case 'while':
                        return statment_test('while(' + param + '){}', 'while (' + param + ') {');
                    case 'for':
                        return statment_test('for(' + param + '){}', 'for (' + param + ') {');
                    case 'each':
                        var match = param.match(/(\w+)\s+(?:(?:as(?:\s+(\w+)))?(?:(?:\s+=>)?\s+(\w+))?)?/);
                        if (match) {
                            var value = match[1];
                            var each_param;
                            if (match[2]) {
                                if (match[3]) {
                                    each_param = match[3] + ',' + match[2];
                                }
                                else {
                                    each_param = match[2];
                                }
                            }
                            else {
                                each_param = 'value,index';
                            }
                            return '$_unit._each(' + value + ',function(' + each_param + '){';
                        }
                        return 'throw SyntaxError("Null Each Value");$_unit._each(null,function(){';
                    case '/each':
                        return '});';
                }
            }
            // 非转义
            else if (match = code.match(/^!.*$/)) {
                return replaces[1] + '$_unit._echo(' + match[1] + ')' + replaces[2];
            }
            // 转义输出
            else {
                return replaces[1] + '$_unit._escape(' + code + ')' + replaces[2];
            }
        }
    }


    var escape_callback = function(s) {
        return escape[s];
    };
    var _echo = function(value) {
        return new String(value);
    }
    var _escape = function(content) {
        return _echo(content).replace(/&(?![\w#]+;)|[<>"']/g, escape_callback);
    };

    var _each = function(value, callback) {
        if (is_array(value)) {
            _arrayEach(value, callback);
        }
        else {
            for (var index in value) {
                callback.call(value[index], value[index], index);
            }
        }
    }
    var _arrayEach = function(value, callback) {
        for (var index = 0; index < value.length; ++index) {
            callback.call(value[index], value[index], index);
        }
    }

    var _include = function(id, value) {
        if (document.getElementById(id)) {
            try {
                var tmp = new template(id, value);
                if (tmp instanceof String) {
                    return tmp;
                }
                return '[Error Template ' + id + ']';
            } catch (e) {
                throw e;
            }
        }
        else
            throw Error('No Template ' + id);
    }

    // 字符串转义
    function _string(code) {
        return "'" + code
            // 单引号与反斜杠转义
            .replace(/('|\\)/g, '\\$1')
            .replace(/\r/g, '\\r')
            .replace(/\n/g, '\\n') + "'";
    }
    function is_object(obj) {
        return Object.prototype.toString.call(obj) === '[object Object]';
    }
    function is_array(obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    }

    var reportError = function(name, content, line, e) {
        var name = name || 'anonymous';
        var report = 'DxTPL Error:';
        console.group(report);
        if (content) {

            var codes = content.replace(/^\n/, '').split('\n');
            var start = line - 5 > 0 ? line - 5 : 1;
            var end = line + 5 > codes.length ? codes.length : line + 5;
            console.error(e);
            // console.log(codes);
            for (var i = start; i < end; i++) {
                if (i == line) {
                    console.log(i + '|%c' + codes[line - 1] + '\t\t%c->\t\t%c' + e.name + ':' + e.message, 'color:red;', 'color:green;', 'color:red;');
                }
                else {
                    console.log(i + '|' + codes[i - 1]);
                }
            }

        }
        else {
            console.log(content);
            console.log('%c' + report + e.message + '\t\t@' + name + ':' + line, 'color:red;');
        }
        console.groupEnd(report);
    }
    var compile = function(text, parsers) {
        var tpl = '';
        // console.log('code',text);
        text = text.replace(/^\n/, '');
        console.log(tagstart);
        _each(text.split(tagstart), function(value, index) {
            // console.log('split',value);
            var split = value.split(tagend);
            if (split.length === 1) {
                tpl += parsers.html(split[0]);
            }
            else {
                tpl += parsers.code(split[0]);
                tpl += parsers.html(split[1]);
            }
        });
        return tpl;
    }

    var link = function(source, value) {
        var ext = [];
        ext.push('var $_unit=this,' + replaces[0]);
        for (var index in value) {
            ext.push(index + '=this.value.' + index);
        }
        var link_str = '';
        if (use_strict) {
            link_str = '"use strict";';
        }
        link_str += ext.join(',');
        link_str += ';';
        link_str += source + 'return new String(' + replaces[3] + ');';
        return link_str;
    }

    var render = function(name, source, compiled_code, value) {
        // console.time('render ' + name);
        var runcode = link(compiled_code, value);
        // console.log(runcode);
        var caller = { _each: _each, _echo: _echo, _escape: _escape, _include: _include, value: value };
        var html;
        try {
            var render = new Function(runcode);
            html = render.call(caller);
        } catch (e) {
            // For Chrome
            var match = new String(e.stack).match(/<anonymous>:(\d+):\d+/);
            // console.log(source);
            // console.log(e);
            if (match) {
                var line = match[1] - 1;
                reportError(name, source, line, e);
            }
            else {
                var name = name || 'anonymous';
                // For Edge
                var match = new String(e.stack).match(/Function code:(\d+):\d+/);
                if (match) {
                    console.error('DxTPL:Compile Error@' + name + ' Line ' + match[1]);
                }
                else {
                    console.error('DxTPL:Compile Error@' + name);
                }
            }

        }
        // console.timeEnd('render ' + name);
        return html;
    }
    var template_Cache;
    var get_cache = function(name) {
        // console.time('getcache:' + name);
        var cache_parent = document.getElementById('template_caches');
        if (!cache_parent) {
            cache_parent = document.createElement('div');
            cache_parent.id = 'template_caches';
            cache_parent.style.display = 'none';
            document.body.appendChild(cache_parent);
        }
        var cache_name = 'template_cache_' + name;

        var tpl_cache = document.getElementById('template_cache_' + name);
        if (!tpl_cache) {
            tpl_cache = document.createElement('div');
            tpl_cache.id = cache_name;
            tpl_cache.innerText = compile(document.getElementById(name).innerHTML, parsers);
            cache_parent.appendChild(tpl_cache);
        }
        // console.timeEnd('getcache:' + name);
        return tpl_cache.innerText;
    }

    var selftpl = function(selector, valueset) {
        var nodes = document.querySelectorAll(selector);
        // console.log(nodes);
        _arrayEach(nodes, function(node, index) {
            var source = node.innerHTML;
            var value;
            if (node.dataset.tplInit) {
                try {
                    var json = new Function('return ' + node.dataset.tplInit + ';');
                    value = json();
                } catch (e) {
                    reportError(selector + '[' + index + ']', null, 0, new Error('Unsupport json'));
                }
            }
            value=object_copy(value,valueset);
            var code = compile(source, parsers);
            node.innerHTML = render(selector, source, code, value);
        });
    }
    var template = function(id, value) {
        if (typeof id !== 'string') throw Error('Unsupport Template ID');
        var tpl = document.getElementById(id);
        var code;
        var source = tpl.innerHTML;
        // console.log(source);
        if (cache) {
            code = get_cache(id);
        }
        else {
            code = compile(source, parsers);
            // console.log('compiled:',code);
        }

        if (value) {
            return render(id, source, code, value);
        }
        else {
            return {

                config: DxTPL.config,
                display: function(value) {
                    return render(id, source, code, value);
                }
            }
        }
    }


    DxTPL.compile = function(content) {
        return {
            display: function(value) {
                return render(null, content,compile(content, parsers),value);
            }
        }
    }
    DxTPL.template = template;
    DxTPL.selftpl = selftpl;
    window.DxTPL = DxTPL;
} ();


!function() {
    var DxDOM, DxUI;
    "use strict";
    var _self_path = function() {
        var scripts = document.getElementsByTagName("script");
        return scripts[scripts.length - 1].getAttribute("src");
    } ();
    var _roor_path = function() {
        return _self_path.substring(0, _self_path.lastIndexOf("/"));
    } ();
    /* --------------- 报错调试 ------------------ */
    window.addEventListener('error', function(e) {
        console.log(e);
        var error = e.message + '\n';
        if (e.error.stack) {
            error += '---- ErrorStack-----\n' + e.error.stack;
        }
        alert(error);
    });
    /* --------------- 全局函数 ------------------ */

    function is_function(obj) {
        return Object.prototype.toString.call(obj) === '[object Function]';
    }
    function is_array(obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    }
    function is_object(obj) {
        return Object.prototype.toString.call(obj) === '[object Object]';
    }
    function is_string(obj) {
        return typeof obj === 'string';
    }
    function get_root_path() {
        var scripts = document.getElementsByTagName("script");
        var _self_path = scripts[scripts.length - 1].getAttribute("src");
        return _self_path.substring(0, _self_path.lastIndexOf("/"));
    }
    // 分发事件
    var dipatch_event = function(obj, name, value, canBubbleArg, cancelAbleArg) {
        var event = document.createEvent(str_cache[0]);
        var canBubble = typeof canBubbleArg === undefined ? true : canBubbleArg;
        var cancelAbl = typeof cancelAbleArg === undefined ? true : cancelAbleArg;
        event.initCustomEvent(name, canBubble, cancelAbl, value);
        obj.dispatchEvent(event);
        if (obj['on' + name] && is_function(obj['on' + name])) {
            obj['on' + name].call(obj, event);
        }
        return event;
    }
    /**
     * 复制合并对象
     * 
     * @param {Object|string} arrays
     * @returns
     */
    function object_copy(arrays) {
        var object = {};
        for (var i = 0; i < arguments.length; i++) {
            for (var index in arguments[i]) {
                object[index] = arguments[i][index];
            }
        }
        return object;
    }

    //CSS 前缀缓存
    var _css_perfix = function get_css_perfix() {
        var styles = window.getComputedStyle(document.documentElement, '');
        var core = (
            Array.prototype.slice
                .call(styles)
                .join('')
                .match(/-(moz|webkit|ms|)-/) || (styles.OLink === '' && ['', 'o'])
        )[1];
        return '-' + core + '-';
    } ();

    /**
     * 添加CSS前缀（如果存在前缀）
     * 
     * @param {string} name
     * @returns 
     */
    function css_prefix(name) {
        name = name.trim();
        name = typeof document.documentElement.style[name] === 'undefined' ? _css_perfix + name : name;
        return name;
    }

    /**
     * 将驼峰式CSS转化成CSS文件用的CSS命名
     * 
     * @param {string} name
     * @returns
     */
    function css_name(name) {
        name = css_prefix(name);
        name = name.replace(/[A-Z]/, function(name) {
            return '-' + name.toLowerCase();
        });
        return name;
    }

    /* ----- DxDOM -------*/
    DxDOM = function(selecter, context) {
        return new DxDOM.static.create(selecter, context);
    }

    DxDOM.static = DxDOM.prototype;
    DxDOM.static.create = function(selecter, context) {
        if (typeof selecter === 'string') {
            this.elements = (context || document).querySelectorAll(selecter);
        }
        else {
            this.elements = [selecter];
        }
        this.context = context;
        this.length = this.elements.length;
        for (var i = 0; i < this.length; i++) {
            this[i] = this.elements[i];
        }
        this.each = function(callback) {
            for (var i = 0; i < this.length; i++) {
                callback.call(this[i], this[i], i);
            }
            return this;
        };
        return this;
    };

    DxDOM.static.extend = function(methods) {
        for (var index in methods) {
            this[index] = methods[index];
        }
    };
    DxDOM.static.create.prototype = DxDOM.static;

    DxDOM.static.extend({
        createElement: function(tag, attr, css) {
            var element = document.createElement(tag);
            DxDOM(element).setAttr(attr).setCss(css);
            return element;
        },
        setAttr: function(attrs) {
            this.each(function() {
                if (attrs) {
                    for (var name in attrs) {
                        this.setAttribute(name, attrs[name]);
                    }
                }
            });
            return this;
        },
        setCss: function(cssObj) {
            this.each(function() {
                if (cssObj) {
                    for (var name in cssObj) {
                        this.style[css_prefix(name)] = cssObj[name];
                    }
                }
            });
            return this;
        },
        addClass: function(add) {
            this.each(function() {
                this.class += ' ' + add;
            });
            return this;
        },
        removeClass: function(remove) {
            this.each(function() {
                var reg = new RegExp('/\\s+?' + remove + '/');
                this.class.replace(reg, '');
            });
            return this;
        },
        on: function(type, listener, useCaptrue) {
            var captrue = typeof useCaptrue === undefined ? true : useCaptrue;
            this.each(function() {
                this.addEventListener(type, listener, useCaptrue);
            });
            return this;
        },
    });
    // 字符串常量
    var str_cache = [
        'CustomEvent',
        'css-insert',
        'Content-Type',
        'toast-parent',
        'application/json',
        'readystatechange',
        'toast_show',
        'toast',
        'win',
        'win-head',
        'win-body',
        'win-head-btn',
        'icon-close',//12
        'win-btns'];
    /*  ----- DxUI 全局可用变量  -----*/
    var DxUI_pop_level = 4000; //弹出层基层
    var DxUI_window_level = 3000; //窗口层基层
    DxUI = {
        ver: '0.1',
        // 加载CSS
        /**
         * 从网络中加载CSS
         * 
         * @param {string} css
         */
        loadCss: function(css) {
            if (typeof css !== 'string') return;
            var path = css;
            if (!/https?:\/\/|\//.test(css)) {
                path = get_root_path() + '/' + css;
            }
            var link = document.createElement('link');
            link.setAttribute('rel', 'stylesheet');
            link.setAttribute('href', path);
            link.id = path.replace(/[^a-zA-Z]/g, '-').replace(/-+/, '-');
            console.log('LoadingCss:' + path);
            document.head.appendChild(link);
        },

        /**
         * 在文档中插入CSS
         * 
         * @param {Object} cssObj
         */
        insertCSS: function(cssObj) {
            var cssNode = document.getElementById(str_cache[1]);
            if (!cssNode) {
                cssNode = document.createElement('style');
                cssNode.id = str_cache[1];
                document.head.appendChild(cssNode);
            }
            var uiobj = this;
            function createCss(cssArray) {
                var text = '{';
                for (var name in cssArray) {
                    text += uiobj.cssName(name);
                    text += ':' + cssArray[name] + ';';
                }
                return text + '}';
            }
            var css = '/* create css ' + name + ' */';
            for (var name in cssObj) {
                css += name + createCss(cssObj[name]);
            }
            cssNode.innerText += css;
        },
        extend: function(object) {
            for (var index in object) {
                this[index] = object[index];
            }
        }
    };
    // Toast队列
    var DxUI_ToastQueue = new Array();

    /**
     * 创建可移动层
     * 
     * @param {Element} layer 移动层
     * @param {Element} controller 控制移动的层
     * @returns
     */
    function DxUI_moveable(layer, controller) {
        var _controller = controller || layer;
        var _self = layer;
        // 调整层可以移动
        _self.style.position = 'fixed';
        var _move_layer = function(event) {
            // 阻止拖动页面（手机端）
            event.preventDefault();
            var eventMove = 'mousemove',
                eventEnd = 'mouseup';
            // 手机触屏事件会成多点触控
            if (event.touches) {
                event = event.touches[0];
                eventMove = 'touchmove';
                eventEnd = 'touchend';
            }
            var rect = _controller.getBoundingClientRect();
            var x = event.clientX - rect.left;
            var y = event.clientY - rect.top;
            // 拖拽
            var doc = document;
            if (_self.setCapture) {
                _self.setCapture();
            }
            else if (window.captureEvents) {
                window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
            }

            // 移动
            var winmove = function(e) {
                if (e.touches) {
                    e = e.touches[0];
                }
                var px = e.pageX || (e.clientX + document.body.scrollLeft - document.body.clientLeft);
                var py = e.pageY || (e.clientY + document.body.scrollTop - document.body.clientTop);

                var dx = px - x;
                var dy = py - y;
                _self.style.left = dx + 'px';
                _self.style.top = dy + 'px';
            };
            // 停止
            var winend = function(e) {
                if (_self.releaseCapture) {
                    _self.releaseCapture();
                }
                else if (window.releaseEvents) {
                    window.releaseEvents(Event.MOUSEMOVE | Event.MOUSEUP);
                }
                doc.removeEventListener(eventMove, winmove);
                doc.removeEventListener(eventEnd, winend);
            };
            doc.addEventListener(eventMove, winmove);
            doc.addEventListener(eventEnd, winend);
        }
        // 监听起始事件
        _controller.addEventListener('mousedown', _move_layer);
        _controller.addEventListener('touchstart', _move_layer);
        return _self;
    }
    var _arrayEach = function(value, callback) {
        for (var index = 0; index < value.length; ++index) {
            callback.call(value[index], value[index], index);
        }
    }

    var DxUI_createbutton = function(config) {
        var attr = object_copy(config); // 复制对象
        // 提取属性
        var type = attr.type || 'button';
        var text = attr.text;
        delete attr.text;
        var on = attr.on || []; //提取事件
        delete attr.on;
        var css = attr.css || {}; // 提取CSS
        delete attr.css;
        var button = DxDOM.static.createElement('button', attr, css);
        button.innerText = text || 'button';
        if (DxUI[type]) {
            button = new DxUI[type](button);
        }
        // console.log(button);
        _arrayEach(on, function(event) {
            if (is_array(event)) {
                DxDOM(button).on(event[0], event[1], event[2]);
            }
            else {
                DxDOM(button).on(event.name, event.callback, event.useCaptrue);
            }
        });
        return button;
    }

    DxUI.extend({
        moveable: DxUI_moveable,
        createButton: DxUI_createbutton,
        /**
         * 弹出一个Toast
         * 
         * @param {string} message 弹出信息
         * @param {number} time 弹出时间
         * @returns
         */
        Toast: function(message, time) {
            var ToastNode = document.getElementById(str_cache[3]);
            if (!ToastNode) {
                ToastNode = document.createElement('div');
                ToastNode.id = str_cache[3];
                document.body.appendChild(ToastNode);
            }

            DxUI_ToastQueue.push({ message: message, timeout: time });
            return {
                show: function showNext() {
                    // 一个时刻只能显示一个Toast
                    if (document.getElementById(str_cache[6])) return;
                    var show = DxUI_ToastQueue.shift();
                    var toastdiv = DxDOM.static.createElement('div', { id: str_cache[6], class: str_cache[7] });
                    toastdiv.innerHTML = show.message;
                    ToastNode.appendChild(toastdiv);
                    var margin = window.innerWidth / 2 - toastdiv.scrollWidth / 2;
                    var bottom = window.innerHeight - toastdiv.scrollHeight * 2;
                    toastdiv.style.marginLeft = margin + 'px';
                    toastdiv.style.top = bottom + 'px';
                    var timeout = show.timeout || 2000;
                    var close = function() {
                        DxDOM(toastdiv).setCss({ 'transition': 'opacity 0.3s ease-out', opacity: 0 });
                        setTimeout(function() {
                            ToastNode.removeChild(toastdiv);
                            if (DxUI_ToastQueue.length) {
                                showNext();
                            }
                        }, 300);
                    };
                    DxDOM(toastdiv).setCss({ position: 'fixed', opacity: 1, 'z-index': DxUI_pop_level, transition: 'opacity 0.1s ease-in' });
                    setTimeout(close, timeout);
                }
            };
        },
        Window: function(config) {
            var _self;
            var _isOpen = false;
            var _close_button;
            _self = DxDOM.static.createElement('div', { class: str_cache[8] });
            _self.config = config;
            var _create_close_button = function(head) {
                _close_button = DxDOM.static.createElement('span', { class: str_cache[11] + ' ' + str_cache[12] });
                _close_button.addEventListener('mousedown', _self.close);
                _close_button.addEventListener('touchstart', _self.close);
                head.appendChild(_close_button);
                return _close_button;
            }

            var _init = function(config) {
                var body = DxDOM.static.createElement('div', { class: str_cache[10] });
                var head = DxDOM.static.createElement('div', { class: str_cache[9] });
                var btns = DxDOM.static.createElement('div', { class: str_cache[13] });
                _self.innerHTML = '';
                _self.config = config || _self.config;
                body.innerHTML = _self.config.content || 'No Content';
                head.innerHTML = _self.config.title || 'No Title';
                btns.innerHTML = '';
                _self.config.moveable = typeof _self.config.moveable === 'undefined' ? true : _self.config.moveable;
                _self.btn = _self.config.btn;

                if (_self.config.moveable) {
                    _self = DxUI_moveable(_self, head);
                }
                _self.appendChild(head);
                _self.appendChild(body);
                _self.appendChild(btns);
                document.body.appendChild(_self);
                body.style.width = body.clientWidth + 'px';
                _self.style.zIndex = DxUI_window_level;
                _self.index = DxUI_window_level;
                DxUI_window_level++;

                if (_self.btn) {
                    for (var index in _self.btn) {
                        var button = _self.btn[index];
                        if (is_string(button)) {
                            switch (button) {
                                case 'close':
                                    _create_close_button(head);
                                    break;
                            }
                        }
                        else {
                            btns.appendChild(DxUI_createbutton(_self.btn[index]));
                        }
                    }
                }
            }

            _self.addEventListener('mousedown', _foucs_window);
            _self.addEventListener('touchstart', _foucs_window);
            _self.isOpen = function() { return _isOpen; };

            _self.open = function open() {
                if (!_isOpen) {
                    _isOpen = true;
                    _init();
                    dipatch_event(_self, 'open');
                }
                return _self;
            }

            _self.close = function(interval) {
                if (interval) {
                    setTimeout(_close_window, interval);
                    return _self;
                }
                else {
                    _close_window();
                    return _self;
                }
            }

            var _close_window = function() {
                if (_isOpen) {
                    var e = dipatch_event(_self, 'close');
                    // 取消关闭窗口
                    if (e.defaultPrevented) return;
                    document.body.removeChild(_self);
                    DxUI_window_level--;
                    _isOpen = false;
                }
            };

            var _foucs_window = function() {
                // window获取焦点，可能会导致层一直递增
                if (_self.style.zIndex < DxUI_window_level) {
                    _self.style.zIndex = DxUI_window_level;
                    _self.index = DxUI_window_level;
                    DxUI_window_level++;
                }
            };

            return _self;
        },
        AjaxButton: function(button) {
            var _self = button;
            _self.addEventListener('click', function() {
                console.log('click me');
                _self.method = _self.getAttribute('method') || "POST";
                _self.action = _self.getAttribute('action');
                var ajax = new XMLHttpRequest();
                ajax.addEventListener(str_cache[5], function() {
                    var event = document.createEvent(str_cache[0]);
                    event.initCustomEvent(str_cache[5], true, true, { xhr: ajax });
                    _self.responseText = ajax.responseText;
                    _self.dispatchEvent(event);
                    if (ajax.readyState == 4) {
                        if (ajax.status == 200) {
                            dipatch_event(_self, 'message', { xhr: ajax, json: JSON.parse(ajax.responseText), text: ajax.responseText, xml: ajax.responseXML });
                        } else {
                            dipatch_event(_self, 'error', { xhr: ajax });
                        }
                    }
                });
                ajax.open(_self.method, _self.action, true);
                var form = document.getElementById(_self.dataset.form);
                if (form) {
                    ajax.send(new FormData(form));
                }
                else {
                    ajax.setRequestHeader(str_cache[2], str_cache[4]);
                    ajax.send(_self.dataset.data);
                }
            });
            return _self;
        }
    });
    // 导出
    window.DxUI = DxUI;
    window.DxDOM = DxDOM;
} ();