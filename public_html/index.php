<?php
require_once('./vendor/autoload.php');
require_once('./logs.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$currentDir = $currentDir = basename(dirname(__FILE__));
$route = new \App\Route($currentDir);

?> 