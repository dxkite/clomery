/* AJAX Use Test */
"use strict",
console.log('---Use AJAX---');
function ajax()
{
    var ajax=new XMLHttpRequest();
    var vars;
    function get(url)
    {
        ajax.open('GET',url);
        return this;
    }
    function values(values)
    {
        console.log(values);
        vars=values;
        return this;
    }
    function ready(callback)
    {
        ajax.send(JSON.stringify(vars));
        return callback();
    }
    return {
        get:get,
        values:values,
        ready:ready,
    };
}
send=new ajax();

send.get('ajax').values({user:'DXkite'}).ready(
    function(answer){
        console.log('hello'+answer);
    }
);