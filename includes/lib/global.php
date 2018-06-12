<?php

function __require($require) {
    switch ($require) {
        case "config":
            include_once(__DIR__."/config.php");
            break;
        case "auth":
            include_once(__DIR__."/auth.php");
            break;
        case "db":
            include_once(__DIR__."/db.php");
            break;
        case "vendor":
            require_once(__DIR__."/../../vendor/autoload.php");
            break;
        case "vendor/sparrow":
            include_once(__DIR__"./../../vendor/mikecao/sparrow/sparrow.php");
            break;
    }
}

?>