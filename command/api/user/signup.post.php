<?php
return api_permision('',function($param){
    return api_check_callback($param,['user','email','password','client_id','client_token'],'api\User::signUp');
});