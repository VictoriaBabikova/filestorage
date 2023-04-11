<?php

require_once "src/autoload.php";
require_once "src/function.php";

session_start();

$arguments = [];
$arguments['id'] = null;
$arguments['email'] = null;
$arguments['password'] = null;
$arguments['first_name'] = null;
$arguments['id_file'] = null;
$arguments['id_dir'] = null;

$URIParts = explode('?', $_SERVER['REQUEST_URI']);

if (! empty($URIParts)) {
    if (isset($URIParts[1])) {
        $params = $URIParts[1];
        $getParamArr = parseApplicationContent($params);
        foreach ($getParamArr as $key => $value) {
            if ($key == "id") {
                $arguments["id"] = $value;
            } elseif ($key == "email") {
                $arguments['email'] = $value;
            } elseif ($key == "password") {
                $arguments['password'] = $value;
            } elseif ($key == "first_name") {
                $arguments['first_name'] = $value;
            } elseif ($key == "id_file") {
                $arguments['id_file'] = $value;
            } elseif ($key == "id_dir") {
                $arguments['id_dir'] = $value;
            }
        }
    }
}


$urlList = [
    // "/" => [
    //     'GET' => "Main::index",
    // ],
    "/user" => [
        'GET' => "User::getUserArray",
        'POST' => "User::addUser", /** params: email, password, ?first_name */
        'PUT' => "User::changeUser", /** params: email, password, ?first_name */
    ],
    "/users?id=". $arguments['id'] => [
        'GET' => "User::getUser",
        'DELETE' => "User::deleteUser",
    ],
    "/login?email=" . $arguments['email']. "&password=" .$arguments['password'] => [
        'GET' => "User::login",
    ],
    "/logout" => [
        'GET' => "User::logout",
    ],
    "/reset_password?email=" . $arguments['email']. "&first_name=" .$arguments['first_name'] => [
        'GET' => "User::resetPassword",
    ],
    "/change_password?email=" . $arguments['email']. "&first_name=" .$arguments['first_name'] => [
        'GET' => "User::changePassword",
    ],
    "/admin/user" => [
        'GET' => "Admin::getUserArray",
        'PUT' => "Admin::changeUser", /** params: email, password, ?first_name */
    ],
    "/admin/user?id=". $arguments['id'] => [
        'GET' => "Admin::getUser",
        'DELETE' => "Admin::deleteUser",
    ],
    "/file" => [
        'GET' => "File::getFilesArray",
        'POST' => "File::addFile", /** params: name_dir, file(uploaded file) */
        'PUT' => "File::changeFile" /** params: (name_file, new_name_file) or (name_file, new_dir) */
    ],
    "/file?id_file=". $arguments['id_file'] => [
        'GET' => "File::getFileInfo",
        'DELETE' => "File::deleteFile",
    ],
    "/directory" => [
        'POST' => "File::addDirectory", /** params: name_dir */
        'PUT' => "File::changeDirectory", /** params: old_name_dir, new_name_dir */
    ],
    "/directory?id_dir=". $arguments['id_dir'] => [
        'GET' => "File::getDirInfo",
        'DELETE' => "File::deleteDir",
    ],
    "/user/search?email=" . $arguments['email'] => [
        'GET' => "File::getUserInfo",
    ],
    "/files/share?id_file=" . $arguments['id_file'] => [
        'GET' => "File::getUserList",
    ],
    "/files/share" => [
        'PUT' => "File::setAccessUser", /** params: id_file, id(user_id) */
    ],
    "/files/share?id_file=" . $arguments['id_file']. "&id=" .$arguments['id'] => [
        'DELETE' => "File::deleteAccessUser",
    ],
];

foreach ($arguments as $key => $argument) {
    if ($argument === null) {
        unset($arguments[$key]);
    }
}

foreach ($urlList as $key => $uri) {
    if ($_SERVER['REQUEST_URI'] === $key) {
        foreach ($uri as $key => $value) {
            if ($_SERVER['REQUEST_METHOD'] === $key) {
                call_user_func('App\\'. $value, ...$arguments);
            }
        }
    }
}
