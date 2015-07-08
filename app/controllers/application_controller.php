<?php

$request_method = $_SERVER['REQUEST_METHOD'];
$_controller = $_GET['_controller'];
$_action = $_GET['_action'];

if (!(($request_method === 'GET' && in_array($_action, array('index', 'show', 'add', 'edit', 'destroy'))) || ($request_method === 'POST' && in_array($_action, array('create', 'update', 'destroy'))))) {
    exit();
}

require_once('../../config/application.php');
require_once('../helpers/application_helper.php');
require_once('../models/activerecord.php');
if (file_exists(ROOT_PATH . '/db/models.php')) {
    require_once(ROOT_PATH . '/db/models.php');
}
if (file_exists(ROOT_PATH . '/db/relations.php')) {
    require_once(ROOT_PATH . '/db/relations.php');
}

set_error_handler(function($id, $message, $file, $line) {
    logger(sprintf('%s|%s|%s|%s', $id, $file, $line, $message));
    throw new ErrorException($message, $id, 0, $file, $line);
});

session_start();

class ApplicationController {
    function __construct() {
        $this->parameters = new Parameter($_POST, $_GET, $_FILES);
        if (empty($this->parameters->format)) {
            $this->parameters->format = 'html';
        }
    }

    // shared variables
    private $variables = array();

    public function __get($name) {
        return $this->variables[$name];
    }

    public function __set($name, $value) {
        $this->variables[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->variables[$name]);
    }

    public function __unset($name) {
        unset($this->variables[$name]);
    }

    // actions

    public $before_action_queue = array(
        'index' => array(),
        'show' => array(),
        'create' => array(),
        'add' => array(),
        'update' => array(),
        'edit' => array(),
        'destroy' => array()
    );

    public function before_action($fn, ...$actions) {
        if (count($actions) > 0) {
            foreach ($actions as $action) {
                $this->before_action_queue[$action][] = $fn;
            }
        } else {
            foreach ($this->before_action_queue as &$queue) {
                $queue[] = $fn;
            }
        }
    }

    public $after_action_queue = array(
        'index' => array(),
        'show' => array(),
        'create' => array(),
        'add' => array(),
        'update' => array(),
        'edit' => array(),
        'destroy' => array()
    );

    public function after_action($fn, ...$actions) {
        if (count($actions) > 0) {
            foreach ($actions as $action) {
                $this->after_action_queue[$action][] = $fn;
            }
        } else {
            foreach ($this->after_action_queue as &$queue) {
                $queue[] = $fn;
            }
        }
    }

    // render

    public $has_rendered = false;

    public function render() {
        global $_controller, $db;
        $args = func_get_args();
        if (func_num_args() == 2) {
            $controller = $args[0];
            $action = $args[1];
        } else {
            $controller = $_controller;
            $action = $args[0];
        }
        $view = "../views/{$controller}/{$action}.{$this->parameters->format}.php";
        if (file_exists($view)) {
            $this->has_rendered = true;
            require($view);
        } else {
            $this->page_not_found();
        }
    }

    public function redirect_to($uri) {
        $this->has_rendered = true;
        header('HTTP/1.1 303 See Other');
        header("Location: {$uri}");
        exit();
    }

    public function page_not_found() {
        $this->has_rendered = true;
        http_response_code(404);
        require_once(RAILS_PATH . '/public/static/404.html');
        exit();
    }

    public function fragment($name) {
        require("../fragments/{$name}.php");
    }

    // resources

    public function index() {}
    public function show() {}
    public function add() {}
    public function create() {}
    public function edit() {}
    public function update() {}
    public function destroy() {}
}

if (file_exists($_controller . '_controller.php')) {
    require_once($_controller . '_controller.php');

    $controller_class_name = String_capitalize(is_a_resource($_controller)? $_controller: singularize($_controller)) . 'Controller';
    $controller = new $controller_class_name();
    $controller->has_rendered = false;

    array_map('call_user_func', $controller->before_action_queue[$_action]);
    if (!$controller->has_rendered) {
        call_user_func(array($controller, $_action));
    }
    if ($request_method === 'GET' && !$controller->has_rendered) {
        $controller->render($_action);
    }
    array_map('call_user_func', $controller->after_action_queue[$_action]);
} else {
    $controller = new ApplicationController();
    $controller->page_not_found();
}
