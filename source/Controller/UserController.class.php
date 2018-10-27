<?php

namespace EatWhat\Controller;

use EatWhat\Base\Controller;

/**
 * user controller for request obj
 * 
 */
class UserController extends Controller
{
    /**
     * generate user date after verifing token
     * 
     */
    private $userData = null;

    /**
     * set access token analyzer
     * 
     */
    public function setUserData($userData)
    {
        $this->userData = $userData;
    }

    /**
     * set user status
     * 
     */
    public function setUserStatus($userStatus)
    {
        $this->userData && ($this->userData["userStatus"] = $userStatus);
    }

    /**
     * get user data
     * 
     */
    public function getUserData()
    {
        return $this->userData;
    }
}