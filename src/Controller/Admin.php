<?php

namespace App;

require_once "src/autoload.php";

class Admin
{
    /**
     * getUserArray return User::getUserArray
     *
     * @return void
     */
    public static function getUserArray()
    {
        if ($_SESSION['ROLE'] === "ROLE_ADMIN") {
            $userDataArray = User::getUserArray();
            return $userDataArray;
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * changeUser return User::changeUser
     *
     * @return void
     */
    public static function changeUser()
    {
        if ($_SESSION['ROLE'] === "ROLE_ADMIN") {
            $userData = User::changeUser();
            return $userData;
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * getUser return getUser($id)
     *
     * @param  integer $id
     * @return void
     */
    public static function getUser(int $id)
    {
        if ($_SESSION['ROLE'] === "ROLE_ADMIN") {
            $userData = User::getUser($id);
            return $userData;
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * deleteUser return deleteUser($id)
     *
     * @param  integer $id
     * @return void
     */
    public static function deleteUser(int $id)
    {
        if ($_SESSION['ROLE'] === "ROLE_ADMIN") {
            $userData = User::deleteUser($id);
            return $userData;
        } else {
            echo $message = "you need login-in";
        }
    }
}
