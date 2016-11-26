<?php

namespace user;


class Session
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user=$user;
    }
    public function isSignIn(){}
    public function isSignUp(){}
    public function who() : User
    {
        return $this->user;
    }
}
