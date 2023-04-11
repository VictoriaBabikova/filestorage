<?php

function loaderClass($className)
{

    $nameClassOnly = explode("\\", $className);
    $nameClassOnly = array_pop($nameClassOnly);
    if (file_exists(__DIR__ . "/" ."Controller" . "/" . $nameClassOnly . ".php")) {
        require_once  __DIR__ . "/" ."Controller" . "/" . $nameClassOnly . ".php";
    }
}

spl_autoload_register('loaderClass');
