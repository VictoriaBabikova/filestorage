<?php

require_once "config.php";

function Connect($user, $password, $base, $host = 'localhost', $port = 3306)
{
    $connectionString = "mysql:host=$host; port=$port; dbname=$base; charset=UTF8";
    $db = new PDO(
        $connectionString,
        $user,
        $password,
        [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION
        ]
    );
    return $db;
}

function Query($query, $param = [])
{
    $db = Connect(LOGIN_DB, PASS_DB, DB_NAME, HOST);
    $res = $db->prepare($query);
    $res->execute($param);
    return $res;
}

function Select($query, $param = [])
{
    $result = Query($query, $param);
    if ($result) {
        return $result->fetchAll();
    }
}


function SelectRow($query, $param = [])
{
    return Select($query, $param);
}
