<?php
namespace user;

class User
{
    protected $uid;
    protected $name;
    protected $email;
    protected $password;
    protected $groupid;
    protected $lastip;
    protected $signup;
    protected $last;
    protected $webtoken;
    protected $apitoken;

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getGroupid()
    {
        return $this->groupid;
    }

    /**
     * @param mixed $groupid
     */
    public function setGroupid($groupid)
    {
        $this->groupid = $groupid;
    }

    /**
     * @return mixed
     */
    public function getLastip()
    {
        return $this->lastip;
    }

    /**
     * @param mixed $lastip
     */
    public function setLastip($lastip)
    {
        $this->lastip = $lastip;
    }

    /**
     * @return mixed
     */
    public function getSignup()
    {
        return $this->signup;
    }

    /**
     * @param mixed $signup
     */
    public function setSignup($signup)
    {
        $this->signup = $signup;
    }

    /**
     * @return mixed
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * @param mixed $last
     */
    public function setLast($last)
    {
        $this->last = $last;
    }

    /**
     * @return mixed
     */
    public function getWebtoken()
    {
        return $this->webtoken;
    }

    /**
     * @param mixed $webtoken
     */
    public function setWebtoken($webtoken)
    {
        $this->webtoken = $webtoken;
    }

    /**
     * @return mixed
     */
    public function getApitoken()
    {
        return $this->apitoken;
    }

    /**
     * @param mixed $apitoken
     */
    public function setApitoken($apitoken)
    {
        $this->apitoken = $apitoken;
    }


}
