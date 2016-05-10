<?php

/* global environment variables */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

ini_set('display_errors', true);
error_reporting(E_ALL);

/* global settings */

date_default_timezone_set('PRC');
set_error_handler(function($id, $message, $file, $line) {
    logger(sprintf('%s|%s|%s|%s', $id, $file, $line, $message));
    throw new ErrorException($message, $id, 0, $file, $line);
});

/* routes */

require_once(__DIR__ . '/routes.php');

/* databases */

if (file_exists(__DIR__ . '/database.php')) {
    require_once(__DIR__ . '/database.php');
}
if (file_exists(ROOT_PATH . '/config/database.php')) {
    require_once(ROOT_PATH . '/config/database.php');
    if (isset($DATABASE)) {
        require_once(ROOT_PATH . '/app/models/activerecord.php');
        $db = DB::open("{$DATABASE['driver']}:host={$DATABASE['host']};port={$DATABASE['port']};dbname={$DATABASE['database']};", $DATABASE['user'], $DATABASE['password']);
        if (file_exists(ROOT_PATH . '/db/models.php')) {
            require_once(ROOT_PATH . '/db/models.php');
        }
        if (file_exists(ROOT_PATH . '/db/relations.php')) {
            require_once(ROOT_PATH . '/db/relations.php');
        }
    }
}
