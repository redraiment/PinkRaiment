<?php

// global environment variables

define('ROOT_PATH', dirname(__DIR__));

ini_set('display_errors', true);
error_reporting(E_ALL);
// global settings

date_default_timezone_set('PRC');

// routes
require_once(__DIR__ . '/routes.php');
