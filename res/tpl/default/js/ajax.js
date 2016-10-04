/* AJAX Use Test */
"use strict",
console.log('---Use Simpile AJAX---');
function ajax()
{
    var ajax=new XMLHttpRequest();
    var vars;
    function get(url)
    {
        ajax.open('GET',url);
        return this;
    }
    function post(url)
    {
        ajax.open('POST',url);
        return this;
    }
    function values(values)
    {
        vars=values;
        return this;
    }
    function ready(callback)
    {
        if (typeof callback==='function')
        {
            ajax.addEventListener('readystatechange',function(){
                if (ajax.readyState===4&&ajax.status===200){
                    callback(JSON.parse(ajax.responseText));
                }
            });
        }
        ajax.setRequestHeader('Content-Type','application/json ; charset=UTF-8');
        ajax.send(JSON.stringify(vars));
    }
    return {
        get:get,
        post:post,
        values:values,
        ready:ready,
    };
}
