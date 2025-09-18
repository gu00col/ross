<?php

session_start();

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/logs.php";

App\EnvLoader::load();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$currentDir = basename(__DIR__);
$route = new \App\Route($currentDir);

?> 