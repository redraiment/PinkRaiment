<?php

// helpers

function helper($name) {
    require_once(ROOT_PATH . "/app/helpers/{$name}_helper.php");
}

function vendor($path) {
    require_once(ROOT_PATH . "/app/vendors/{$path}");
}

// logger

function logger($message) {
    $filename = ROOT_PATH . '/logs/' . date('Ymd') . '.log';
    file_put_contents($filename, date('[H:i:s]') . $message . "\n", FILE_APPEND);
}

// convertion

$resource = ['home', 'session', 'profile', 'password', 'search', 'statistics'];

function is_a_resource($word) {
    global $resource;
    return in_array($word, $resource);
}

$plurals = [ // <= resources
    'license' => 'licenses',
    'enterprise' => 'enterprises',
    'child' => 'children',
    'foot' => 'feet',
    'half' => 'halves',
    'holiday' => 'holidays',
    'knife' => 'knives',
    'leaf' => 'leaves',
    'life' => 'lives',
    'man' => 'men',
    'monkey' => 'monkeys',
    'mouse' => 'mice',
    'potato' => 'potatoes',
    'thief' => 'thieves',
    'tomato' => 'tomatoes',
    'tooth' => 'teeth',
    'wife' => 'wives',
    'wolf' => 'wolves',
    'woman' => 'women'
];

function pluralize($word) {
    global $plurals;
    if (isset($plurals[$word])) {
        return $plurals[$word];
    } elseif (preg_match('/(?:s|sh|ch|x)$/', $word)) {
        return $word . 'es';
    } elseif (preg_match('/[^aeiou]y/', $word)) {
        return substr($word, 0, -1) . 'ies';
    } else {
        return $word . 's';
    }
}

$singulars = array_flip($plurals);

function singularize($word) {
    global $singulars;
    if (isset($singulars[$word])) {
        return $singulars[$word];
    } elseif (preg_match('/(?:s|sh|ch|x)es$/', $word)) {
        return substr($word, 0, -2);
    } elseif (preg_match('/[^aeiou]ies$/', $word)) {
        return substr($word, 0, -3) . 'y';
    } else {
        return substr($word, 0, -1);
    }
}

function String_downcase($string) {
    return strtolower($string);
}

function String_upcase($string) {
    return strtoupper($string);
}

function String_capitalize($string) {
    return ucfirst($string);
}

function String_camel_case($string) {
    return implode('', array_map('String_capitalize', explode("_", $string)));
}

function String_underscore($string) {
    return implode('_', array_map('String_downcase', preg_split('/(?<=[a-z])(?=[A-Z])/', $string)));
}

// [ed]ncode

function Encode_url($var) {
    if (is_array($var)) {
        $result = array();
        foreach ($var as $key => $value) {
            $result[Encode_url($key)] = Encode_url($value);
        }
        return $result;
    } else {
        return urlencode($var);
    }
}

function Decode_url($string) {
    return urldecode($string);
}

function Encode_json($var) {
    return Decode_url(json_encode(Encode_url($var)));
}

function Decode_json($string) {
    return json_decode($string, true);
}

// Flash Messages

function flash_has($name) {
    $key = 'flash-' . $name;
    return isset($_SESSION[$key]);
}

function flash(...$args) {
    if (count($args) === 1) {
        list($name) = $args;
        $key = 'flash-' . $name;
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        } else {
            return '';
        }
    } elseif (count($args) === 2) {
        list($name, $value) = $args;
        $key = 'flash-' . $name;
        $_SESSION[$key] = $value;
    }
}

// parameters

class Parameter {
    private $overrides;
    private $requests;
    private $defaults;

    function __construct(...$requests) {
        $this->overrides = [];
        $this->requests = $requests;
        $this->defaults = [];
    }

    public function __isset($name) {
        if (isset($this->overrides[$name])) {
            return true;
        }
        foreach ($this->requests as $request) {
            if (isset($request[$name])) {
                return true;
            }
        }
        if (isset($this->defaults[$name])) {
            return true;
        }
        return false;
    }

    public function __set($name, $value) {
        $this->overrides[$name] = $value;
    }

    public function raw($name) {
        $value = null;
        $requests = array_merge([$this->overrides], $this->requests, [$this->defaults]);
        foreach ($requests as $request) {
            if (isset($request[$name])) {
                $value = $request[$name];
                break;
            }
        }
        return $value;
    }

    public function __get($name) {
        $value = $this->raw($name);
        return (is_numeric($value) && preg_match('/^[1-9]/', $value))? ($value + 0): $value;
    }

    public function optional($name, $value) {
        $this->defaults[$name] = $value;
    }

    public function required(...$names) {
        foreach ($names as $name) {
            if (!$this->__isset($name)) {
                exit();
            }
        }
    }
}

// assets

function h($content) {
    return htmlspecialchars($content);
}

function last_page() {
    if (isset($_GET['from'])) {
        return $_GET['from'];
    } elseif (isset($_SERVER['REQUEST_URI'])) {
        return $_SERVER['REQUEST_URI'];
    } else {
        return ROOT_PATH;
    }
}
