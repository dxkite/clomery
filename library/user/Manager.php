<?php
namespace user;

use archive\Manager as AManager;

class Manager
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user=$user;
    }

    public function signUp()
    {
        $this->user->password=password_hash($this->user->password, PASSWORD_DEFAULT);
        $manager=new AManager($this->user);
        $this->user->uid=$manager->insert();
        var_dump($this->user);
    }
    
    public function signIn()
    {

    }
    public function signOut()
    {

    }
}
