<?php
namespace View;
use \View;
/**
 * 
 */
class Includer  implements \Env_Method
{
    var $args=[];
    function setParams(array $arguments)
    {
        $this->args=$arguments;
    }
    function render()
    {
        call_user_func_array(['Page','render'],$this->args); 
    }
}
