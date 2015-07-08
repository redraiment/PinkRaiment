<?php

// global environment variables

define('ROOT_PATH', dirname(__DIR__));

ini_set('display_errors', true);
error_reporting(E_ALL);
// global settings

date_default_timezone_set('PRC');
if (file_exists(__DIR__ . '/database.php')) {
    require_once(__DIR__ . '/database.php');
}

// routes
require_once(__DIR__ . '/routes.php');
